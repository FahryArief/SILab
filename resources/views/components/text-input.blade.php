@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10 rounded-xl shadow-sm text-gray-700 text-sm placeholder-gray-400 py-3 px-4 transition-all duration-200 hover:border-gray-300']) }}>
