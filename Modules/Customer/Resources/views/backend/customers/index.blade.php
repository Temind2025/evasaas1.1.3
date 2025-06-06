@extends('backend.layouts.app')

@section('title')
{{ __($module_action) }} {{ __($module_title) }}
@endsection


@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush
@section('content')
<div class="card">
  <div class="card-body">
    <x-backend.section-header>
      <div class="d-flex flex-wrap gap-3">
        @if(auth()->user()->can('edit_customer') || auth()->user()->can('delete_customer'))
        <x-backend.quick-action url='{{route("backend.$module_name.bulk_action")}}'>
          <div class="">
            <select name="action_type" class="form-control select2 col-12" id="quick-action-type" style="width:100%">
              <option value="">{{ __('messages.no_action') }}</option>
              @can('edit_customer')
              <option value="change-status">{{ __('messages.status') }}</option>
              @endcan
              @can('delete_customer')
            <option value="delete">{{ __('messages.delete') }}</option>
            @endcan
            </select>
          </div>
          <div class="select-status d-none quick-action-field" id="change-status-action">
            <select name="status" class="form-control select2" id="status" style="width:100%">
              <option value="1">{{ __('messages.active') }}</option>
              <option value="0">{{ __('messages.inactive') }}</option>
            </select>
          </div>
        </x-backend.quick-action>
        @endif
        <div>
          <button type="button" class="btn btn-secondary" data-modal="export">
            <i class="fa-solid fa-download"></i> {{ __('messages.export') }}
          </button>

        </div>
      </div>

      <x-slot name="toolbar">
        <div class="input-group flex-nowrap top-input-search">
          <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
            aria-describedby="addon-wrapping">
        </div>
        @hasPermission('add_customer')
         
              <x-buttons.offcanvas class="customer-create-btn" target='#form-offcanvas' title="">{{ __('messages.new') }}</x-buttons.offcanvas>
         
        @endhasPermission
      </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-striped border table-responsive">
    </table>
  </div>
</div>
<div data-render="app">
  <customer-offcanvas default-image="{{default_user_avatar()}}" create-title="{{ __('messages.new') }} {{ __('customer.singular_title') }}"
    edit-title="{{ __('messages.edit') }} {{ __('customer.singular_title') }}" :customefield="{{ json_encode($customefield) }}">
  </customer-offcanvas>
  <change-password create-title="{{ __('messages.change_password') }}"></change-password>
</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')


<script src="{{ mix('modules/customer/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script>
$(document).ready(function () {
        // Hide offcanvas initially
        const formOffcanvas = document.getElementById("form-offcanvas");
        const offcanvasInstance = bootstrap.Offcanvas.getInstance(formOffcanvas) || new bootstrap.Offcanvas(formOffcanvas);
        offcanvasInstance.hide();
    $(document).on("click", ".customer-create-btn", function (event) {
        let button = $(this); // Store reference to button
        $.ajax({
            url: "{{ route('backend.customers.verify') }}", // Ensure this route exists
            type: "GET",
            data: { type: 'customer' },
            dataType: "json",
            success: function (response) {
                if (!response.status) {
                    event.preventDefault(); // Prevent default action
                    window.errorSnackbar(response.message);
                    button.removeAttr("data-crud-id"); // Remove attribute if status is false
                    offcanvasInstance.hide();
                } else {
                    button.attr("data-crud-id", 0); // Set a valid value if required
                    offcanvasInstance.show(); // Show the offcanvas only if allowed
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });
});



</script>
<script type="text/javascript" defer>
  const columns = [{
        name: 'check',
        data: 'check',
        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
        width: '0%',
        exportable: false,
        orderable: false,
        searchable: false,
      },

    {
        data: 'user_id',
        name: 'user_id',
        title: "{{ __('booking.lbl_customer_name') }}"
    },
  
      
      {
        data: 'gender',
        name: 'gender',
        title: "{{ __('customer.lbl_gender') }}"
      },
     
      {
        data: 'email_verified_at',
        name: 'email_verified_at',
        orderable: false,
        searchable: true,
        title: "{{ __('customer.lbl_verification_status') }}"
      },
     
      {
        data: 'status',
        name: 'status',
        orderable: false,
        searchable: true,
        title: "{{ __('customer.lbl_status') }}"
      },
      {
        data: 'updated_at',
        name: 'updated_at',
        title: "{{ __('customer.lbl_update_at') }}",
        orderable: true,
        visible: false,
       },
    ]

    const actionColumn = [{
      data: 'action',
      name: 'action',
      orderable: false,
      searchable: false,
      title: "{{ __('customer.lbl_action') }}"
    }]

    const customFieldColumns = JSON.parse(@json($columns))

    let finalColumns = [
      ...columns,
      ...customFieldColumns,
      ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
      initDatatable({
        url: '{{ route("backend.$module_name.index_data") }}',
        finalColumns,
        orderColumn: [[ 6, "desc" ]],

      })
    })

    function resetQuickAction() {
      const actionValue = $('#quick-action-type').val();
      if (actionValue != '') {
        $('#quick-action-apply').removeAttr('disabled');

        if (actionValue == 'change-status') {
          $('.quick-action-field').addClass('d-none');
          $('#change-status-action').removeClass('d-none');
        } else {
          $('.quick-action-field').addClass('d-none');
        }
      } else {
        $('#quick-action-apply').attr('disabled', true);
        $('.quick-action-field').addClass('d-none');
      }
    }

    $('#quick-action-type').change(function() {
      resetQuickAction()
    });
</script>
@endpush