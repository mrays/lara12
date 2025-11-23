<nav class="layout-navbar navbar navbar-expand-xl navbar-detached bg-navbar-theme">
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item">
                <span class="nav-link">Hi, {{ auth()->user()->name }}</span>
            </li>
        </ul>

    </div>
</nav>
