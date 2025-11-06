<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Quản lý phòng ban & Trưởng phòng</h1>

        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">Mã</th>
                    <th class="px-3 py-2">Tên phòng</th>
                    <th class="px-3 py-2">Trưởng phòng</th>
                    <th class="px-3 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dep)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $dep->code }}</td>
                        <td class="px-3 py-2">{{ $dep->name }}</td>
                        <td class="px-3 py-2">{{ $dep->manager->name ?? '-' }}</td>
                        <td class="px-3 py-2">
                            <a href="#" class="text-blue-600 hover:underline">Gán trưởng phòng</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
