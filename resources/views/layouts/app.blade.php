<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ setting('store_name', 'Warung POS') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f6f9; margin: 0; }

        /* ============ SIDEBAR ============ */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #1e293b;
            color: #cbd5e1;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform .3s;
            display: flex;
            flex-direction: column;
        }

        /* Scrollbar tipis */
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: #0f172a; }
        .sidebar::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: #64748b; }

        .sidebar .brand {
            padding: 18px;
            font-weight: 700;
            color: #fff;
            background: #0f172a;
            font-size: 1.05rem;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid #334155;
        }
        .sidebar .brand i { color: #38bdf8; margin-right: 6px; }

        .sidebar nav {
            flex: 1;
            padding-bottom: 70px; /* ruang untuk tombol scroll */
        }

        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 10px 18px;
            display: block;
            font-size: .92rem;
            transition: all .15s;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            background: #334155;
            color: #fff;
            border-left-color: #38bdf8;
        }
        .sidebar a.active {
            background: #334155;
            color: #fff;
            border-left-color: #38bdf8;
            font-weight: 600;
        }
        .sidebar a i { margin-right: 8px; width: 18px; text-align: center; }

        .sidebar .nav-section {
            font-size: .7rem;
            color: #64748b;
            padding: 14px 18px 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Tombol scroll sticky di bawah */
        .sidebar-scroll-buttons {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: #0f172a;
            padding: 8px 12px;
            display: flex;
            gap: 8px;
            border-top: 1px solid #334155;
            margin-top: auto;
        }
        .sidebar-scroll-buttons button {
            flex: 1;
            background: #334155;
            color: #cbd5e1;
            border: none;
            border-radius: 6px;
            padding: 6px 0;
            cursor: pointer;
            font-size: 1rem;
            transition: all .2s;
        }
        .sidebar-scroll-buttons button:hover {
            background: #38bdf8;
            color: #fff;
        }

        /* ============ MAIN CONTENT ============ */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 12px 20px;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
            border-radius: 10px;
        }
        .stat-card {
            border-radius: 12px;
            color: #fff;
        }
        .stat-card small { opacity: .9; font-size: .78rem; }
        .stat-card h4 { font-weight: 700; }

        /* ============ MOBILE ============ */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,.5);
            z-index: 999;
        }
        .sidebar-backdrop.show { display: block; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { padding: 10px 14px; }
        }

        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- ============ SIDEBAR ============ --}}
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-shop"></i> {{ setting('store_name', 'Warung POS') }}
        </div>

        <nav class="mt-2">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            @if(in_array(auth()->user()->role->name ?? '', ['admin','kasir']))
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i> Kasir
            </a>
            @endif

            <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Transaksi
            </a>

            @if(in_array(auth()->user()->role->name ?? '', ['admin','owner']))
            <div class="nav-section">Master Data</div>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Kategori
            </a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Supplier
            </a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pelanggan
            </a>

            <div class="nav-section">Inventori</div>
            <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <i class="bi bi-archive"></i> Inventory
            </a>
            <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                <i class="bi bi-bag-check"></i> Pembelian
            </a>

            <div class="nav-section">Keuangan</div>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Pengeluaran
            </a>
            <a href="{{ route('cash.index') }}" class="{{ request()->routeIs('cash.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Kas
            </a>

            <div class="nav-section">Laporan</div>
            <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Penjualan
            </a>
            <a href="{{ route('reports.purchases') }}" class="{{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
                <i class="bi bi-bag"></i> Pembelian
            </a>
            <a href="{{ route('reports.profit') }}" class="{{ request()->routeIs('reports.profit') ? 'active' : '' }}">
                <i class="bi bi-currency-dollar"></i> Keuntungan
            </a>
            <a href="{{ route('reports.stock') }}" class="{{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i> Stok
            </a>
            <a href="{{ route('reports.bestseller') }}" class="{{ request()->routeIs('reports.bestseller') ? 'active' : '' }}">
                <i class="bi bi-star"></i> Produk Terlaris
            </a>
            <a href="{{ route('reports.expense') }}" class="{{ request()->routeIs('reports.expense') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Pengeluaran
            </a>
            <a href="{{ route('reports.cash') }}" class="{{ request()->routeIs('reports.cash') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Kas
            </a>
            <a href="{{ route('reports.customer') }}" class="{{ request()->routeIs('reports.customer') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Pelanggan
            </a>

            <div class="nav-section">Sistem</div>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
            @endif

            @if((auth()->user()->role->name ?? '') === 'admin')
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> User
            </a>
            @endif
        </nav>

        {{-- Tombol scroll naik/turun --}}
        <div class="sidebar-scroll-buttons">
            <button type="button" onclick="scrollSidebar(-250)" title="Geser ke atas">
                <i class="bi bi-chevron-up"></i>
            </button>
            <button type="button" onclick="scrollSidebar(250)" title="Geser ke bawah">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
    </div>

    {{-- Backdrop mobile --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    {{-- ============ MAIN ============ --}}
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <strong>@yield('title', 'Dashboard')</strong>
            </div>
                        <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none text-dark" data-bs-toggle="dropdown" href="#" role="button">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role->name ?? '-') }})
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                            <i class="bi bi-person"></i> Profil & Ubah Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="p-3 p-md-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarBackdrop').classList.toggle('show');
        }

        // Scroll sidebar naik/turun
        function scrollSidebar(offset) {
            document.getElementById('sidebar').scrollBy({
                top: offset,
                behavior: 'smooth'
            });
        }

        // Auto-scroll ke menu aktif saat halaman dibuka
        document.addEventListener('DOMContentLoaded', function () {
            const activeLink = document.querySelector('.sidebar a.active');
            if (activeLink) {
                const sidebar = document.getElementById('sidebar');
                const linkTop = activeLink.offsetTop;
                const sidebarHeight = sidebar.clientHeight;
                sidebar.scrollTop = linkTop - (sidebarHeight / 2);
            }
        });

        // SweetAlert notifikasi session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                timer: 2500,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error'))
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: @json(session('warning'))
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>