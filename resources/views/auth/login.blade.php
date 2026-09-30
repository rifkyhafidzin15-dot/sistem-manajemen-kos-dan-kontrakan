<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0B0F17] text-slate-100 antialiased selection:bg-indigo-500/30 selection:text-indigo-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Pengelola | Kost & Kontrakan Management</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow-indigo': '0 0 35px -5px rgba(99, 102, 241, 0.3)',
                        'subtle-card': '0 20px 40px -15px rgba(0, 0, 0, 0.7)',
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full bg-[#0B0F17] flex items-center justify-center p-4 relative overflow-hidden font-sans"
      x-data="{
          email: '{{ old('email') }}',
          password: '',
          fillAccount(emailVal) {
              this.email = emailVal;
              this.password = 'password123';
          }
      }">

    <!-- AMBIENT BACKGROUND LIGHTING -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-gradient-to-b from-indigo-600/15 via-indigo-900/5 to-transparent blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/4 w-[500px] h-[400px] bg-emerald-500/10 blur-3xl"></div>
    </div>

    <main class="w-full max-w-md relative z-10 py-10">
        
        <!-- CARD CONTAINER -->
        <section class="rounded-3xl bg-[#111827]/90 backdrop-blur-2xl border border-slate-800 p-8 sm:p-10 shadow-subtle-card">
            
            <!-- Back Link -->
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                <span>← Lihat kost & kontrakan</span>
            </a>

            <!-- Header Identity -->
            <div class="mt-8 space-y-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-400 p-[1px] shadow-glow-indigo">
                        <div class="w-full h-full bg-[#111827] rounded-[11px] flex items-center justify-center font-bold text-white text-sm">
                            K
                        </div>
                    </div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-400 font-mono">Area privat</span>
                </div>
                
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Masuk pengelola</h1>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Gunakan akun aktif yang diberikan oleh pemilik sistem untuk mengelola properti, unit kamar, dan laporan finansial.
                </p>
            </div>

            <!-- LOGIN FORM -->
            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Email Pengelola</label>
                    <div class="relative">
                        <input id="email" 
                               name="email" 
                               type="email" 
                               x-model="email"
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="nama@kost.com"
                               class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-4 py-3 text-xs sm:text-sm text-white placeholder:text-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition-all">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" 
                               name="password" 
                               type="password" 
                               x-model="password"
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••"
                               class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-4 py-3 text-xs sm:text-sm text-white placeholder:text-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition-all">
                    </div>
                </div>

                <!-- Remember & Security -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-xs text-slate-400 cursor-pointer select-none">
                        <input name="remember" type="checkbox" value="1" class="w-4 h-4 rounded bg-slate-900 border-slate-800 text-indigo-600 focus:ring-0 focus:ring-offset-0">
                        <span>Ingat saya</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Enkripsi 256-bit</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 px-4 py-3.5 text-xs sm:text-sm font-bold text-white shadow-glow-indigo transition-all duration-200">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- DEMO SHORTCUT / HELPER BOX FOR CONVENIENCE -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 space-y-3">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                    Akses Cepat Testing:
                </span>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            @click="fillAccount('owner@kost.com')"
                            class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/40 text-left transition-colors">
                        <span class="text-xs font-bold text-slate-200 block">Owner</span>
                        <span class="text-[10px] text-slate-400 font-mono block">owner@kost.com</span>
                    </button>
                    <button type="button" 
                            @click="fillAccount('staff@kost.com')"
                            class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/40 text-left transition-colors">
                        <span class="text-xs font-bold text-slate-200 block">Staff</span>
                        <span class="text-[10px] text-slate-400 font-mono block">staff@kost.com</span>
                    </button>
                </div>
            </div>

        </section>

        <!-- Footer System Note -->
        <p class="mt-6 text-center text-xs text-slate-400 font-mono">
            KostOS Platform v2.4 • Private Authentication
        </p>
    </main>

</body>
</html>