<x-app-layout>

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
                                {{-- <button @click="remove(flow.id)" class="text-red-600 hover:underline">Xóa</button>
                                --}}
                                @can('manage', App\Models\ApprovalFlow::class)
                                    <button @click="remove(flow.id)" class="text-red-600 hover:underline">Xóa</button>
                                @else
                                    <span class="text-gray-400 italic">Không có quyền</span>
                                @endcan
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="mt-4">
                {{-- <button @click="showModal = true"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + Thêm flow mới
                </button> --}}
                @can('manage', App\Models\ApprovalFlow::class)
                    <button @click="showModal = true" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        + Thêm flow mới
                    </button>
                @endcan
            </div>

            <!-- MODAL -->
            <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
                x-transition>
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6" @click.away="showModal = false">
                    <h2 class="text-lg font-semibold mb-4">Thêm Flow mới</h2>
                    <form @submit.prevent="save">
                        <div class="mb-3">
                            <label class="block text-sm font-medium">Module</label>
                            <input type="text" x-model="form.module" class="w-full mt-1 border-gray-300 rounded-md"
                                placeholder="Ví dụ: leave">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium">Phòng ban</label>
                            <select x-model="form.department_id" class="w-full mt-1 border-gray-300 rounded-md">
                                <option value="">-- Chọn phòng ban --</option>
                                @foreach($departments as $dep)
                                    <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium">Vai trò phê duyệt</label>
                            <input type="text" x-model="form.role" class="w-full mt-1 border-gray-300 rounded-md"
                                placeholder="VD: HR_Manager">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium">Cấp duyệt (Level)</label>
                            <input type="number" min="1" x-model="form.level"
                                class="w-full mt-1 border-gray-300 rounded-md">
                        </div>
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 border rounded">Hủy</button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approvalFlow() {
            return {
                flows: [],
                departmentId: '',
                showModal: false,
                form: {
                    module: 'leave',
                    role: '',
                    department_id: '',
                    level: 1,
                    approver_role_id: 1, // tạm cứng nếu chưa có bảng roles
                },
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
                save() {
                    fetch('/api/admin/approval-flows', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    })
                        .then(res => res.json())
                        .then(() => {
                            this.showModal = false;
                            this.load();
                            this.form = { module: 'leave', role: '', department_id: '', level: 1, approver_role_id: 1 };
                        });
                }
            }
        }
    </script>

</x-app-layout>
