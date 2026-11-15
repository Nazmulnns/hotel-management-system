<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    */

    'required' => ':attribute দিতে হবে।',
    'string' => ':attribute অবশ্যই লেখা হতে হবে।',
    'integer' => ':attribute অবশ্যই পূর্ণ সংখ্যা হতে হবে।',
    'numeric' => ':attribute অবশ্যই সংখ্যা হতে হবে।',
    'boolean' => ':attribute অবশ্যই true অথবা false হতে হবে।',
    'array' => ':attribute অবশ্যই একটি তালিকা হতে হবে।',
    'email' => ':attribute সঠিক ইমেইল হতে হবে।',
    'date' => ':attribute সঠিক তারিখ হতে হবে।',
    'date_format' => ':attribute অবশ্যই :format ফরম্যাটে হতে হবে।',
    'after' => ':attribute অবশ্যই :date এর পরে হতে হবে।',
    'after_or_equal' => ':attribute অবশ্যই :date অথবা তার পরে হতে হবে।',
    'before' => ':attribute অবশ্যই :date এর আগে হতে হবে।',
    'confirmed' => ':attribute মিলছে না।',
    'different' => ':attribute এবং :other আলাদা হতে হবে।',
    'unique' => 'এই :attribute আগে থেকেই ব্যবহার হয়েছে।',
    'exists' => 'নির্বাচিত :attribute পাওয়া যায়নি।',
    'in' => 'নির্বাচিত :attribute সঠিক নয়।',
    'enum' => 'নির্বাচিত :attribute সঠিক নয়।',
    'distinct' => ':attribute এ একই মান দুইবার দেওয়া যাবে না।',
    'regex' => ':attribute এর ফরম্যাট সঠিক নয়।',
    'image' => ':attribute অবশ্যই ছবি হতে হবে।',
    'mimes' => ':attribute অবশ্যই :values ধরনের ফাইল হতে হবে।',
    'current_password' => 'বর্তমান পাসওয়ার্ড ভুল।',

    'min' => [
        'numeric' => ':attribute কমপক্ষে :min হতে হবে।',
        'string' => ':attribute কমপক্ষে :min অক্ষরের হতে হবে।',
        'array' => ':attribute এ কমপক্ষে :min টি থাকতে হবে।',
        'file' => ':attribute কমপক্ষে :min কিলোবাইট হতে হবে।',
    ],

    'max' => [
        'numeric' => ':attribute :max এর বেশি হতে পারবে না।',
        'string' => ':attribute :max অক্ষরের বেশি হতে পারবে না।',
        'array' => ':attribute এ :max টির বেশি থাকতে পারবে না।',
        'file' => ':attribute :max কিলোবাইটের বেশি হতে পারবে না।',
    ],

    'between' => [
        'numeric' => ':attribute অবশ্যই :min থেকে :max এর মধ্যে হতে হবে।',
        'string' => ':attribute অবশ্যই :min থেকে :max অক্ষরের মধ্যে হতে হবে।',
    ],

    'password' => [
        'letters' => ':attribute এ অন্তত একটি অক্ষর থাকতে হবে।',
        'mixed' => ':attribute এ অন্তত একটি বড় হাতের ও একটি ছোট হাতের অক্ষর থাকতে হবে।',
        'numbers' => ':attribute এ অন্তত একটি সংখ্যা থাকতে হবে।',
        'symbols' => ':attribute এ অন্তত একটি চিহ্ন থাকতে হবে।',
        'uncompromised' => 'এই :attribute ফাঁস হয়ে গেছে। অন্য :attribute দিন।',
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Names
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'নাম',
        'full_name' => 'পুরো নাম',
        'email' => 'ইমেইল',
        'phone_number' => 'ফোন নম্বর',
        'password' => 'পাসওয়ার্ড',
        'current_password' => 'বর্তমান পাসওয়ার্ড',
        'role' => 'পদ',
        'status' => 'অবস্থা',
        'issue' => 'সমস্যা',
        'issue_description' => 'সমস্যার বিবরণ',
        'room_id' => 'রুম',
        'guest_id' => 'অতিথি',
        'check_in' => 'চেক-ইন তারিখ',
        'check_out' => 'চেক-আউট তারিখ',
        'rooms' => 'রুম',
        'from' => 'শুরুর তারিখ',
        'to' => 'শেষের তারিখ',
        'year' => 'বছর',
        'token' => 'টোকেন',
        'images' => 'ছবি',
    ],

];