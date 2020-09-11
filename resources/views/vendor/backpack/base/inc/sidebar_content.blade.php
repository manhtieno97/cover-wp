<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}
    </a>
</li>
<li class='nav-item'>
    <a class='nav-link' href='{{ backpack_url('get-cover') }}'>
        <i class='nav-icon la la-question'></i> Get cover
    </a>
</li>
<li class='nav-item'>
    <a class='nav-link' href='{{ backpack_url('cover') }}'>
        <i class='nav-icon la la-question'></i> Covers Table
    </a>
</li>
