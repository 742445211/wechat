<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkAdd extends FormRequest
{
    public function authorize()
    {
        return true;   //默认false   改为true   禁止访问方法
    }
    public function rules()
    {
        return [
            //表单验证规则
			'title' => 'required|regex:/\w{1,18}/',
			'content' => 'required|max:150',
			'price' => 'required',
			'address' => 'required',
			'contacts' => 'required',
			'phone' => 'required|regex:/\d{11}/',
			'group' => 'required|regex:/\w{1,18}/'
        ];
    }
	public function messages()
	{
		//自定义错误提示
		return[
			'title.required' => '标题不能为空',
			'title.regex' => '标题为1-18位数字字母下划线组成',
			'content.required' => '内容不能为空',
			'content.max' => '内容必须为180位以内数字字母下划线组成',
			'price.required' => '薪资不能为空',
			'address.required' => '坐标不能为空',
			'contacts.required' => '联系人不能为空',
			'phone.required' => '电话不能为空',
			'phone.regex' => '电话格式不正确',
			'group.required' => '公司不能为空',
			'group.regex' => '公司长度为1-18位字符'
			
		
		];
	}
}
