<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item active">
        <a href="{{ route('admin.dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Data Master</span>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-data"></i>
            <div data-i18n="Account Settings">Data Master</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('admin.admin.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Notifications">Data Admin</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.kategori.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-category"></i>
                    <div data-i18n="Notifications">Data Kategori</div>
                </a>
            </li>
            {{-- <li class="menu-item">
                <a href="{{ route('admin.vip.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-crown"></i>
                    <div data-i18n="Notifications">Data VIP</div>
                </a>
            </li> --}}
            <li class="menu-item">
                <a href="{{ route('admin.produk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-package"></i>
                    <div data-i18n="Account">Data Produk</div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item">
        <a href="{{ route('admin.deposit.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-arrow-to-bottom"></i>
            <div data-i18n="Tables">Data Deposit</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="{{ route('admin.withdraw.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-arrow-to-top"></i>
            <div data-i18n="Tables">Data Withdraw</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ route('admin.midtrans_settings.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div data-i18n="Tables">Setting Api Midtrans</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ route('admin.user.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div data-i18n="Tables">Manajemen Pengguna</div>
        </a>
    </li>
</ul>
