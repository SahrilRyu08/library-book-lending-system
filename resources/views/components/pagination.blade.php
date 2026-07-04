@if ($paginator->hasPages())

    <nav class="d-flex justify-content-center mt-4">

        <ul class="pagination moco-pagination">

            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">

                <a class="page-link"
                   href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}"
                   tabindex="{{ $paginator->onFirstPage() ? '-1' : '' }}">

                    <i class="bi bi-chevron-left"></i>

                </a>

            </li>


            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)

                @if (is_string($element))

                    <li class="page-item disabled">

                    <span class="page-link">
                        {{ $element }}
                    </span>

                    </li>

                @endif


                @if (is_array($element))

                    @foreach ($element as $page => $url)

                        <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">

                            <a class="page-link"
                               href="{{ $url }}">

                                {{ $page }}

                            </a>

                        </li>

                    @endforeach

                @endif

            @endforeach


            {{-- Next --}}
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">

                <a class="page-link"
                   href="{{ $paginator->hasMorePages() ? $paginator->nextPageUrl() : '#' }}">

                    <i class="bi bi-chevron-right"></i>

                </a>

            </li>

        </ul>

    </nav>

@endif
