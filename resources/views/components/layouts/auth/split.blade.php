<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="absolute inset-0 bg-neutral-900 overflow-hidden">
                    <canvas id="matrix-canvas" class="absolute inset-0 w-full h-full text-neutral-600 dark:text-neutral-600"></canvas>
                </div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    {{ config('app.name') }}
                </a>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
        
        <script>
        function initMatrixEffect() {
            const canvas = document.getElementById('matrix-canvas');
            if (!canvas) return null;
            
            const ctx = canvas.getContext('2d');
            
            function resizeCanvas() {
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            
            const matrix = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()*&^%+-/~{[|`]}";
            const matrixArray = matrix.split("");
            
            const fontSize = 14;
            const columns = canvas.width / fontSize;
            const drops = Array(Math.floor(columns)).fill(1);
            
            // Get text color from canvas element's computed color
            function getTextColor() {
                return getComputedStyle(canvas).color || '#10b981';
            }
            
            function draw() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                ctx.fillStyle = getTextColor();
                ctx.font = fontSize + 'px monospace';

                for (let i = 0; i < drops.length; i++) {
                    const text = matrixArray[Math.floor(Math.random() * matrixArray.length)];
                    ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                    
                    if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                        drops[i] = 0;
                    }
                    drops[i]++;
                }
            }
            
            // Control the speed
            return setInterval(draw, 55);
        }
        
        var matrixInterval = null;
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            matrixInterval = initMatrixEffect();
        });
        
        // Re-initialize on Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            if (matrixInterval) {
                clearInterval(matrixInterval);
            }
            matrixInterval = initMatrixEffect();
        });
        
        // Cleanup on navigation
        window.addEventListener('beforeunload', () => {
            if (matrixInterval) {
                clearInterval(matrixInterval);
            }
        });
        </script>
    </body>
</html>
