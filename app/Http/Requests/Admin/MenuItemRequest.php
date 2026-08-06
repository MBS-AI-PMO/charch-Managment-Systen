<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage-menus') ?? false;
    }

    public function rules(): array
    {
        return [
            'label' => 'required|string|max:120',
            'link_type' => 'required|in:page,url,route',
            'link_value' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'target' => 'nullable|in:_self,_blank',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
