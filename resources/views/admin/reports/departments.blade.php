<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto" x-data="departmentReport()">
        <h1 class="text-2xl font-bold mb-4">Báo cáo nghỉ phép theo phòng ban</h1>

        <div class="flex gap-4 mb-4">
            <select x-model="departmentId" class="rounded-md border-gray-300">
                <option value="">-- Tất cả phòng ban --</option>
                @foreach($departments as $dep)
                    <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                @endforeach
            </select>

            <input type="month" x-model="month" class="border-gray-300 rounded-md">
            <button @click="load()" class="bg-blue-500 text-white px-4 py-2 rounded">Xem báo cáo</button>
        </div>

        <table class="min-w-full border border-gray-200 mt-2">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">Phòng ban</th>
                    <th class="px-3 py-2">Tổng đơn</th>
                    <th class="px-3 py-2">Đã duyệt</th>
                    <th class="px-3 py-2">Từ chối</th>
                    <th class="px-3 py-2">Tổng ngày nghỉ</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="row in reports" :key="row.department_id">
                    <tr class="border-t">
                        <td class="px-3 py-2" x-text="row.department_name"></td>
                        <td class="px-3 py-2 text-center" x-text="row.total_requests"></td>
                        <td class="px-3 py-2 text-center" x-text="row.approved_count"></td>
                        <td class="px-3 py-2 text-center" x-text="row.rejected_count"></td>
                        <td class="px-3 py-2 text-center" x-text="row.total_days"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <script>
        function departmentReport() {
            return {
                reports: [],
                departmentId: '',
                month: new Date().toISOString().slice(0, 7),
                load() {
                    fetch(`/api/reports/departments/leave-summary?department_id=${this.departmentId}&month=${this.month}`)
                        .then(res => res.json())
                        .then(data => this.reports = data.data || []);
                },
                init() { this.load(); }
            }
        }
    </script>
</x-app-layout>
