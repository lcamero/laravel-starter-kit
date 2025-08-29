<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css/github-markdown.min.css">
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <div class="w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0 font-light dark:text-white max-w-4xl mx-auto space-y-10">
            <img src="{{ asset('starter-kit.png') }}" class="w-1/3 sm:w-1/5 max-w-xl mx-auto" />
            <article class="markdown-body dark:text-white! dark:bg-black!">
                {!! $readme !!}
            </article>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.markdown-body pre').forEach(pre => {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('relative');
                    pre.parentNode.insertBefore(wrapper, pre);
                    wrapper.appendChild(pre);

                    const button = document.createElement('button');
                    button.textContent = 'Copy';
                    
                    button.classList.add(
                        'absolute', 'top-2', 'right-2', 'px-2', 'py-1', 'text-xs', 'font-semibold',
                        'text-gray-800', 'bg-gray-200', 'border', 'border-gray-300', 'rounded-md',
                        'hover:bg-gray-300', 'dark:text-gray-200', 'dark:bg-gray-700',
                        'dark:border-gray-600', 'dark:hover:bg-gray-600', 'opacity-0', 'transition-opacity', 'duration-200'
                    );

                    wrapper.addEventListener('mouseenter', () => {
                        button.classList.remove('opacity-0');
                    });

                    wrapper.addEventListener('mouseleave', () => {
                        button.classList.add('opacity-0');
                    });

                    wrapper.appendChild(button);

                    button.addEventListener('click', () => {
                        const code = pre.querySelector('code')?.innerText || pre.innerText;
                        navigator.clipboard.writeText(code).then(() => {
                            button.textContent = 'Copied!';
                            setTimeout(() => {
                                button.textContent = 'Copy';
                            }, 2000);
                        }).catch(err => {
                            console.error('Failed to copy text: ', err);
                        });
                    });
                });
            });
        </script>
    </body>
</html>
