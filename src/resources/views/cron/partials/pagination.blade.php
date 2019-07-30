<nav aria-label="...">
    <ul class="pagination justify-content-center">
        <li class="page-item {{ $current == 1 ? 'disabled' : '' }} ">
            <a class="page-link" href="?page={{ $current - 1 }}" tabindex="-1">
                <i class="fa fa-angle-left"></i>
                <span class="sr-only">Previous</span>
            </a>
        </li>

        @for ($i = 1; $i <= $pages; $i++)
            <li class="page-item {{ $i == $current ? 'active' : '' }}">
                <a class="page-link" href="?page={{ $i }}">
                    {{ $i }}
                    @if ($i == $current)
                        <span class="sr-only">(current)</span>
                    @endif
                </a>
            </li>
        @endfor

        <li class="page-item {{ $current == $pages ? 'disabled' : '' }}">
            <a class="page-link" href="?page={{ $current + 1 }}">
                <i class="fa fa-angle-right"></i>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>
