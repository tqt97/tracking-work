<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Quản lý Flow phê duyệt</h1>

        <div class="mb-4">
            <label class="block font-medium text-sm text-gray-700">Chọn phòng ban:</label>
            <select x-model="departmentId" class="mt-1 block w-full rounded-md border-gray-300">
                <option value="">-- Tất cả --</option>
                @foreach($departments as $dep)
                    <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                @endforeach
            </select>
        </div>

        <div x-data="approvalFlow()">
            <table class="min-w-full border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-3 py-2">Level</th>
                        <th class="px-3 py-2">Vai trò duyệt</th>
                        <th class="px-3 py-2">Phòng ban</th>
                        <th class="px-3 py-2">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="flow in flows" :key="flow.id">
                        <tr class="border-t">
                            <td class="px-3 py-2" x-text="flow.level"></td>
                            <td class="px-3 py-2" x-text="flow.approver_role_name"></td>
                            <td class="px-3 py-2" x-text="flow.department_name || '-'"></td>
                            <td class="px-3 py-2">
                                <button @click="remove(flow.id)" class="text-red-600 hover:underline">Xóa</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="mt-4">
                <button @click="add()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Thêm
                    dòng</button>
            </div>
        </div>
    </div>

    <script>
        function approvalFlow() {
            return {
                flows: [],
                departmentId: '',
                init() {
                    this.load();
                    this.$watch('departmentId', () => this.load());
                },
                load() {
                    fetch(`/api/admin/approval-flows?department_id=${this.departmentId || ''}`)
                        .then(res => res.json())
                        .then(data => this.flows = data.data || []);
                },
                remove(id) {
                    if (confirm('Xóa flow này?')) {
                        fetch(`/api/admin/approval-flows/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).then(() => this.load());
                    }
                },
                add() {
                    alert('Sẽ hiển thị modal thêm flow mới (bổ sung sau).');
                }
            }
        }
    </script>
</x-app-layout>
