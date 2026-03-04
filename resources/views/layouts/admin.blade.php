<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'Admin Dashboard') - Lab RPL</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #18181b !important;
            color: white !important;
            border: none !important;
            border-radius: 0.5rem !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e4e4e7 !important;
            border-radius: 0.5rem !important;
            padding: 0.4rem 0.8rem !important;
            outline: none !important;
        }
    </style>
</head>

<body class="bg-[#f8f9fa] font-sans text-zinc-900 antialiased overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar -->
            @include('layouts.partials.navbar')

            <!-- Page Content -->
            <main id="main-content" class="flex-1 overflow-y-auto px-6 py-6">
                @yield('content')
            </main>

            <!-- Footer Fixed at Bottom -->
            @include('layouts.partials.footer')
        </div>
    </div>

    @yield('scripts')

    <script>
        // SPA Custom Logic
        document.addEventListener('click', async (e) => {
            const link = e.target.closest('a[data-spa]');
            if (link) {
                e.preventDefault();
                const url = link.href;
                await navigateTo(url);
            }
        });

        async function navigateTo(url) {
            try {
                const response = await fetch(url);
                const htmlText = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');

                const newContent = doc.getElementById('main-content').innerHTML;
                document.getElementById('main-content').innerHTML = newContent;

                const newTitle = doc.querySelector('title').innerText;
                document.title = newTitle;

                window.history.pushState({}, '', url);

                // Re-initialize scripts if needed (like DataTables)
                initializePlugins();
            } catch (error) {
                console.error('Navigation failed:', error);
                window.location.href = url; // Fallback
            }
        }

        window.addEventListener('popstate', () => {
            navigateTo(window.location.href);
        });

        function initializePlugins() {
            if ($.fn.DataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable().destroy();
            }
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    }
                });
            }
        }

        $(document).ready(function() {
            initializePlugins();
        });
    </script>
</body>

</html>
