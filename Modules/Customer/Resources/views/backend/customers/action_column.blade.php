<div class="d-flex gap-2 align-items-center">
    @hasPermission('customer_password')
        <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password'
            data-assign-event='employee_assign' class='btn btn-secondary btn-sm rounded text-nowrap' data-bs-toggle="tooltip"
            title="{{ __('messages.change_password') }}"><i class="fas fa-key"></i></button>
    @endhasPermission

    @hasPermission('edit_customer')
        <button type="button" class="btn btn-primary btn-sm" data-crud-id="{{ $data->id }}"
            title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    @endhasPermission
    @hasPermission('delete_customer')
        <a href="{{ route("backend.$module_name.destroy", $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
            class="btn btn-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', ['module' => __('customer.singular_title'), 'name' => $data->full_name]) }}">
            <i class="fa-solid fa-trash"></i></a>
    @endhasPermission
</div>
