@extends('admin.layouts.master')

@section('title', __('Add Role'))

@section('head_style')
  <link rel="stylesheet" type="text/css" href="{{ asset('public/admin/customs/css/custom-checkbox.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('public/admin/customs/css/role-form-shell.min.css') }}">
@endsection

@section('page_content')
  <div class="row">
      <div class="col-md-3 settings_bar_gap">
        @include('admin.common.settings_bar')
      </div>
      <div class="col-md-9">
        <div class="box box-info role-add-shell">
          <div class="box-header with-border">
            <h3 class="box-title">{{ __('Add Role') }}</h3>
            <p class="role-add-lead">{{ __('Define the role identity, then assign permissions. Use “Select all” to grant every permission at once, or pick individual actions per module.') }}</p>
          </div>

          <form method="POST" action="{{ url(config('adminPrefix').'/settings/add_role') }}" class="form-horizontal" id="roles_add_form">
              {{ csrf_field() }}

              <div class="box-body">
                  <div class="role-add-section">
                    <h4 class="role-add-section-title">{{ __('Role details') }}</h4>

                    <div class="form-group row">
                      <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="name">{{ __('Name') }}</label>
                      <div class="col-sm-7">
                        <input type="text" name="name" class="form-control f-14" value="{{ old('name') }}" placeholder="{{ __('Internal name (letters only)') }}" id="name" autocomplete="off">
                        @if($errors->has('name'))
                          <span class="help-block">
                            <strong class="text-danger">{{ $errors->first('name') }}</strong>
                          </span>
                        @endif
                        <span id="name-error"></span>
                        <span id="name-ok" class="text-success"></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="display_name">{{ __('Display Name') }}</label>
                      <div class="col-sm-7">
                        <input type="text" name="display_name" class="form-control f-14" value="{{ old('display_name') }}" placeholder="{{ __('Shown to administrators') }}" id="display_name" autocomplete="off">
                        @if($errors->has('display_name'))
                          <span class="help-block">
                            <strong class="text-danger">{{ $errors->first('display_name') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-sm-3 control-label f-14 fw-bold text-sm-end" for="description">{{ __('Description') }}</label>
                      <div class="col-sm-7">
                        <textarea name="description" id="description" placeholder="{{ __('Short summary of what this role is for') }}" rows="3" class="form-control f-14">{{ old('description') }}</textarea>
                        @if($errors->has('description'))
                          <span class="help-block">
                            <strong class="text-danger">{{ $errors->first('description') }}</strong>
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>

                  <div class="role-add-section">
                    <h4 class="role-add-section-title">{{ __('Permissions') }}</h4>

                    <div class="form-group row">
                      <div class="col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1">
                          <div class="role-add-perms-panel">
                            <div class="role-add-perms-intro">
                              <h4>{{ __('Permission matrix') }}</h4>
                              <p>{{ __('Toggle View, Add, Edit, or Delete for each area. Selecting Add, Edit, or Delete will also enable View for that row.') }}</p>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-bordered f-14 role-add-perm-table">
                                <thead>
                                  <tr>
                                    <th colspan="5">
                                      <label class="checkbox-container mb-0 f-14 fw-bold" for="select_all_permissions" style="cursor: pointer;">
                                        <input type="checkbox" id="select_all_permissions" autocomplete="off">
                                        <span class="checkmark"></span>
                                        <span style="margin-left: 6px; vertical-align: middle;">{{ __('Select all permissions') }}</span>
                                      </label>
                                    </th>
                                  </tr>
                                  <tr>
                                    <th>{{ __('Module / area') }}</th>
                                    <th>{{ __('View') }}</th>
                                    <th>{{ __('Add') }}</th>
                                    <th>{{ __('Edit') }}</th>
                                    <th>{{ __('Delete') }}</th>
                                  </tr>
                                </thead>

                                <tbody>
                                  @foreach ($perm as $key => $value)
                                    <tr data-rel="{{ $loop->index }}">
                                      <td>{{ $key }}</td>

                                      @foreach ($value as $i => $v)
                                          <input type="hidden" value="{{ $v['user_type'] }}" name="user_type" id="user_type">
                                          <input type="hidden" value="{{ $v['id'] }}" name="id" id="id">

                                          @if (isset($v['display_name']))
                                            <td>
                                              <label class="checkbox-container">
                                                <input type="checkbox" name="permission[]" id="{{'view_'.$i}}" value="{{ $v['id'] }}"
                                                class="{{ ($i % 4 == 0) ? 'view_checkbox' :'other_checkbox' }}">
                                                <span class="checkmark"></span>
                                              </label>
                                            </td>
                                          @else
                                            <td></td>
                                          @endif
                                      @endforeach
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                            <div id="error-message"></div>
                          </div>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="box-footer">
                <a class="btn btn-theme-danger f-14" href="{{ url(config('adminPrefix').'/settings/roles') }}">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-theme f-14">{{ __('Add') }}</button>
              </div>
          </form>
        </div>
      </div>
  </div>
@endsection

@push('extra_body_scripts')

<script src="{{ asset('public/dist/plugins/jquery-validation/dist/jquery.validate.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
  function syncSelectAllPermissionsMaster() {
    var $boxes = $('input[name="permission[]"]');
    var total = $boxes.length;
    var checked = $boxes.filter(':checked').length;
    $('#select_all_permissions').prop('checked', total > 0 && checked === total);
  }

  $(document).on('change', '#select_all_permissions', function () {
    var on = $(this).prop('checked');
    $('input[name="permission[]"]').prop('checked', on);
  });

  $(document).on('change', 'input[name="permission[]"]', function () {
    syncSelectAllPermissionsMaster();
  });

  $(document).on('change','.other_checkbox',function()
  {
      var tr_id        = $(this).closest('tr').attr('data-rel');
      var fieldInputId = 'view_'+tr_id;
      $("#"+fieldInputId).prop("checked",true);
      syncSelectAllPermissionsMaster();
  });

  jQuery.validator.addMethod("letters_with_spaces", function(value, element)
  {
    return this.optional(element) || /^[A-Za-z ]+$/i.test(value);
  }, "Please enter letters only!");

  $.validator.setDefaults({
      highlight: function(element) {
        $(element).parent('div').addClass('has-error');
      },
      unhighlight: function(element) {
       $(element).parent('div').removeClass('has-error');
     },
     errorPlacement: function (error, element) {
      if (element.prop('type') === 'checkbox') {
        $('#error-message').html(error);
      } else {
        error.insertAfter(element);
      }
    }
  });

  $('#roles_add_form').validate({
    rules: {
      name: {
        required: true,
        letters_with_spaces: true,
      },
      display_name: {
        required: true,
        letters_with_spaces: true,
      },
      description: {
        required: true,
        letters_with_spaces: true,
      },
      "permission[]": {
        required: true,
        minlength: 1
      },
    },
    messages: {
      "permission[]": {
        required: "Please select at least one checkbox!",
      },
    },
  });

  $(document).ready(function()
  {
      $("#name").on('input', function(e)
      {
        var name = $('#name').val();
        var user_type = $('#user_type').val();
        $.ajax({
            headers:
            {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: SITE_URL+"/"+ADMIN_PREFIX+"/settings/roles/duplicate-role-check",
            dataType: "json",
            data: {
                'name': name,
                'user_type': user_type,
            }
        })
        .done(function(response)
        {
            if (response.status == true)
            {
                emptyName();
                $('#name-error').show();
                $('#name-error').addClass('error').html(response.fail).css("font-weight", "bold");
                $('form').find("button[type='submit']").prop('disabled',true);
            }
            else if (response.status == false)
            {
                $('#name-error').html('');
                $('form').find("button[type='submit']").prop('disabled',false);
            }

            function emptyName() {
                if( name.length === 0 )
                {
                    $('#name-error').html('');
                }
            }
        });
      });

      syncSelectAllPermissionsMaster();
  });
</script>

@endpush
