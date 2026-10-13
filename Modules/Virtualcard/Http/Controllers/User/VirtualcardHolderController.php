<?php

namespace Modules\Virtualcard\Http\Controllers\User;

use Spatie\QueryBuilder\{
    AllowedFilter,
    QueryBuilder
};
use Modules\Virtualcard\{
    Entities\VirtualcardHolder,
    Events\VirtualcardApplication,
    Actions\UpsertVirtualcardHolderAction,
    DataTransferObject\VirtualcardHolderData,
    Http\Requests\UpsertVirtualcardHolderRequest
};
use App\Models\Country;
use Carbon\Carbon, Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class VirtualcardHolderController extends Controller
{
    public function __construct(

        private readonly UpsertVirtualcardHolderAction $upsertCardHolder

    ) {}

    public function index()
    {
        $data['applications'] = QueryBuilder::for(VirtualcardHolder::class)
                                    ->allowedFilters([
                                        'preferred_currency',
                                        'status',
                                        AllowedFilter::exact('type', 'card_holder_type'),
                                        AllowedFilter::callback('from', function ($query, $value) {
                                            $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay() );
                                        }),
                                        AllowedFilter::callback('to', function ($query, $value) {
                                            $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay() );
                                        }),
                                    ])
                                    ->where('user_id', auth()->id())
                                    ->orderBy('id', 'desc')
                                    ->paginate(10);

        $data['statuses'] = VirtualcardHolder::select('status')->distinct()->get();
        $data['filter'] = request()->input('filter');

        return view('virtualcard::user.virtualcard_holders.index', $data);
    }

    public function create()
    {

        $data['countries'] = Country::all(['id', 'short_name', 'name']);
        $data['user'] = auth()->user()->select(['id', 'defaultCountry'])->first();


        return view('virtualcard::user.virtualcard_holders.create', $data);
    }

    public function store(UpsertVirtualcardHolderRequest $request): RedirectResponse
    {
        try {
            if (auth()->user()->virtualcardHolders->where('status', 'Approved')->count() >= preference('holder_limit')) {
                return redirect()
                    ->route('user.virtualcard_holder.index')
                    ->with('error', __('You have reached the limit of card holder creation.'));
            }

            $applicationData = $this->upsert($request, new VirtualcardHolder());
            event(new VirtualcardApplication($applicationData));
            return redirect()
                ->route('user.virtualcard_holder.index')
                ->with('success', __('Application is submitted successfully.'));

        } catch (Exception $e) {

            return redirect()
                ->route('user.virtualcard_holder.create')
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return redirect()->route('user.virtualcard_holder.index')->with('success', __('Application submitted successfully'));
    }

    public function show(VirtualcardHolder $virtualcardHolder)
    {
        return view('virtualcard::user.virtualcard_holders.create',[

            'virtualCardHolder' => $virtualcardHolder

        ]);
    }

    public function edit(VirtualcardHolder $virtualcardHolder)
    {
        return view('virtualcard::user.virtualcard_holders.edit', [

            'virtualCardHolder' => $virtualcardHolder

        ]);
    }

    public function update(

        VirtualcardHolder $virtualcardHolder,
        UpsertVirtualcardHolderRequest $request

    ): RedirectResponse {

        $this->upsert($request, $virtualcardHolder);
        return redirect()->route('user.virtualcard_holder.index')->with('success', __('Application updated successfully'));

    }

    private function upsert (

        UpsertVirtualcardHolderRequest $request,
        VirtualcardHolder $virtualcardHolder

    ): VirtualcardHolder {

        $virtualcardHolderData = VirtualcardHolderData::fromRequest($request);
        return $this->upsertCardHolder->execute($virtualcardHolder, $virtualcardHolderData);

    }

}
