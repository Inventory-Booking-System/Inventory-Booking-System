<x-layouts.base>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div style="background:#222; min-height:100vh">
            <div class="content-header">
                <div class="container-fluid">
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.base>