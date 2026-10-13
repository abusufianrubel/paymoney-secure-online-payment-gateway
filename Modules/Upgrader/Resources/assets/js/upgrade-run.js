'use strict';

(function () {
    const app = document.getElementById('upgrade-app');

    if (!app) {
        return;
    }

    const filesUrl = app.dataset.filesUrl;
    const finalizeUrl = app.dataset.finalizeUrl;
    const homeUrl = app.dataset.homeUrl;

    const logEl = document.getElementById('upgrade-log');
    const logEmpty = document.getElementById('upgrade-log-empty');
    const progressBar = document.getElementById('upgrade-progress-bar');
    const statusBadge = document.getElementById('upgrade-status-badge');
    const clockEl = document.getElementById('upgrade-clock');
    const actionsEl = document.getElementById('upgrade-actions');
    const retryBtn = document.getElementById('upgrade-retry-btn');
    const homeBtn = document.getElementById('upgrade-home-btn');
    const backBtn = document.getElementById('upgrade-back-btn');

    const stepEls = document.querySelectorAll('.upgrade-step');

    let filesPhaseDone = false;
    let currentProgress = 8;
    let creepTimer = null;
    let progressAnimFrame = null;

    function updateClock() {
        const now = new Date();
        clockEl.textContent = now.toLocaleTimeString();
    }

    setInterval(updateClock, 1000);
    updateClock();

    function applyProgressWidth(percent) {
        currentProgress = Math.min(100, Math.max(0, percent));
        progressBar.style.width = currentProgress + '%';
    }

    function stopProgressMotion() {
        if (creepTimer) {
            clearInterval(creepTimer);
            creepTimer = null;
        }

        if (progressAnimFrame) {
            cancelAnimationFrame(progressAnimFrame);
            progressAnimFrame = null;
        }
    }

    function animateProgressTo(target, durationMs) {
        stopProgressMotion();

        target = Math.min(100, Math.max(0, target));

        return new Promise(function (resolve) {
            const start = currentProgress;
            const delta = target - start;

            if (Math.abs(delta) < 0.25) {
                applyProgressWidth(target);
                resolve();
                return;
            }

            const duration = durationMs || Math.min(1200, Math.max(400, Math.abs(delta) * 25));
            const startTime = performance.now();

            function frame(now) {
                const elapsed = now - startTime;
                const t = Math.min(1, elapsed / duration);
                const eased = 1 - Math.pow(1 - t, 3);
                applyProgressWidth(start + delta * eased);

                if (t < 1) {
                    progressAnimFrame = requestAnimationFrame(frame);
                } else {
                    progressAnimFrame = null;
                    applyProgressWidth(target);
                    resolve();
                }
            }

            progressAnimFrame = requestAnimationFrame(frame);
        });
    }

    /** Slowly tick upward while a long-running phase is in flight. */
    function startProgressCreep(cap, stepSize, intervalMs) {
        stopProgressMotion();

        creepTimer = setInterval(function () {
            if (currentProgress < cap) {
                applyProgressWidth(Math.min(cap, currentProgress + stepSize));
            }
        }, intervalMs || 280);
    }

    function setProgress(percent, options) {
        const opts = options || {};

        if (opts.instant) {
            stopProgressMotion();
            applyProgressWidth(percent);
            return Promise.resolve();
        }

        return animateProgressTo(percent, opts.duration);
    }

    function setStep(activeStep) {
        const order = ['prepare', 'files', 'database', 'finish'];
        const activeIndex = order.indexOf(activeStep);

        stepEls.forEach(function (el) {
            const step = el.dataset.step;
            const index = order.indexOf(step);

            el.classList.remove('is-active', 'is-done');

            if (index < activeIndex) {
                el.classList.add('is-done');
            } else if (index === activeIndex) {
                el.classList.add('is-active');
            }
        });
    }

    function setBadge(text, type) {
        statusBadge.textContent = text;
        statusBadge.className = 'upgrade-badge upgrade-badge--' + type;
    }

    function clearEmptyState() {
        if (logEmpty) {
            logEmpty.remove();
        }
    }

    function appendLogs(logs) {
        if (!Array.isArray(logs)) {
            return;
        }

        clearEmptyState();

        logs.forEach(function (entry) {
            const line = document.createElement('div');
            line.className = 'upgrade-log-line upgrade-log-line--' + (entry.level || 'info');

            const time = document.createElement('span');
            time.className = 'upgrade-log-time';
            time.textContent = entry.time || '--:--:--';

            const msg = document.createElement('span');
            msg.className = 'upgrade-log-msg';
            msg.textContent = entry.message || '';

            line.appendChild(time);
            line.appendChild(msg);
            logEl.appendChild(line);
        });

        logEl.scrollTop = logEl.scrollHeight;
    }

    function showActions(options) {
        actionsEl.classList.remove('d-none');
        retryBtn.classList.add('d-none');
        homeBtn.classList.add('d-none');
        backBtn.classList.remove('d-none');

        if (options.retry) {
            retryBtn.classList.remove('d-none');
        }

        if (options.home) {
            homeBtn.classList.remove('d-none');
            backBtn.classList.add('d-none');
        }
    }

    async function fetchPhase(url) {
        const response = await fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        let data;

        const responseText = (await response.text()).trim();

        try {
            data = JSON.parse(responseText);
        } catch (e) {
            if (response.status === 503 || responseText.indexOf('Service Unavailable') !== -1) {
                throw new Error(
                    'Service Unavailable (maintenance mode). Refresh this page and click "Retry database phase".'
                );
            }

            if (response.redirected || responseText.indexOf('<!DOCTYPE') !== -1 || responseText.indexOf('<html') !== -1) {
                if (responseText.toLowerCase().indexOf('login') !== -1) {
                    throw new Error(
                        'Your admin session expired. Please sign in again, re-upload the update package, and retry.'
                    );
                }

                throw new Error(
                    'The server returned a web page instead of upgrade data (HTTP ' + response.status + '). ' +
                    'This often happens when the session was not ready on the first attempt — upload the package again or refresh and retry.'
                );
            }

            throw new Error('Invalid server response. Check server logs.');
        }

        if (Array.isArray(data.logs)) {
            appendLogs(data.logs);
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Upgrade phase failed.');
        }

        return data;
    }

    async function runFilesPhase() {
        setStep('files');
        await setProgress(20);
        appendLogs([{
            step: 'files',
            message: 'Starting file update (delete & copy)…',
            level: 'info',
            time: new Date().toLocaleTimeString(),
        }]);

        startProgressCreep(47, 0.65, 260);
        await fetchPhase(filesUrl);
        stopProgressMotion();
        filesPhaseDone = true;
        await setProgress(50);
    }

    async function runFinalizePhase() {
        setStep('database');
        await setProgress(55);
        appendLogs([{
            step: 'database',
            message: 'Starting database update & cleanup…',
            level: 'info',
            time: new Date().toLocaleTimeString(),
        }]);

        startProgressCreep(92, 0.55, 300);
        await fetchPhase(finalizeUrl);
        stopProgressMotion();
        setStep('finish');
        await setProgress(100, { duration: 900 });
    }

    async function runUpgrade() {
        try {
            setStep('prepare');
            startProgressCreep(18, 0.4, 320);

            await runFilesPhase();
            await runFinalizePhase();

            setBadge('Completed', 'success');
            showActions({ home: true });

            appendLogs([{
                step: 'complete',
                message: 'Redirecting to dashboard in 5 seconds…',
                level: 'success',
                time: new Date().toLocaleTimeString(),
            }]);

            setTimeout(function () {
                window.location.href = homeUrl;
            }, 5000);
        } catch (error) {
            stopProgressMotion();
            setBadge('Failed', 'error');
            setProgress(filesPhaseDone ? 55 : 20, { instant: false });

            appendLogs([{
                step: 'error',
                message: error.message || 'An unknown error occurred.',
                level: 'error',
                time: new Date().toLocaleTimeString(),
            }]);

            if (filesPhaseDone) {
                setStep('database');
                showActions({ retry: true });
            } else {
                setStep('files');
                showActions({ retry: false });
            }
        }
    }

    retryBtn.addEventListener('click', async function () {
        retryBtn.disabled = true;

        try {
            setBadge('In progress', 'live');
            setStep('database');
            await setProgress(55);
            startProgressCreep(92, 0.55, 300);
            await fetchPhase(finalizeUrl);
            stopProgressMotion();
            setStep('finish');
            await setProgress(100, { duration: 900 });
            setBadge('Completed', 'success');
            showActions({ home: true });

            setTimeout(function () {
                window.location.href = homeUrl;
            }, 5000);
        } catch (error) {
            setBadge('Failed', 'error');
            appendLogs([{
                step: 'error',
                message: error.message || 'Retry failed.',
                level: 'error',
                time: new Date().toLocaleTimeString(),
            }]);
        } finally {
            retryBtn.disabled = false;
        }
    });

    runUpgrade();
})();
