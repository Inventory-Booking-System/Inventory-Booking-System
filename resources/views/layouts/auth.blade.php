<x-layouts.base>
    <!-- Content Wrapper. Contains page content -->
    <div style="
        background: rgb(2,0,36);
        background: linear-gradient(360deg, rgb(43, 36, 175) 0%, rgba(60,158,223,1) 35%, rgba(0,212,255,1) 100%);
    ">
        <div style="height:100vh">
            {{ $slot }}
        </div>
    </div>
</x-layouts.base>