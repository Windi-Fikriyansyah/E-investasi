<!-- resources/views/layouts/sidebar.blade.php -->
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="nav-item active">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </a>

    <a href="{{ route('referral.index') }}" class="nav-item active">
        <i class="fas fa-user-friends"></i>
        <span>Undangan</span>
    </a>
    <a href="{{ route('produk.index') }}" class="nav-item">
        <i class="fas fa-chart-line"></i>
        <span>Investasi</span>
    </a>
    <a href="{{ route('pesanan') }}" class="nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span>Pesanan</span>
    </a>
    <a href="{{ route('bonus.index') }}" class="nav-item">
        <i class="fas fa-users"></i>
        <span>Tim</span>
    </a>
</nav>
