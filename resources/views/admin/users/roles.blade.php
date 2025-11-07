<x-app-layout>

    <div class="p-6 max-w-7xl mx-auto" x-data="userRoleManager()">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Quản lý Vai trò người dùng</h1>

            <div class="flex items-center gap-2">
                <!-- Filter -->
                <select x-model="filterRole" @change="applyFilter"
                    class="border-gray-300 rounded-md text-sm px-2 py-1.5 focus:ring-blue-300">
                    <option value="">Tất cả vai trò</option>
                    <template x-for="r in roles" :key="r.id">
                        <option :value="r.id" x-text="r.name"></option>
                    </template>
                </select>

                <!-- Import -->
                <form method="POST" action="{{ route('admin.users.roles.import') }}" enctype="multipart/form-data"
                    class="inline-block">
                    @csrf
                    <label
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm px-3 py-1.5 rounded-md cursor-pointer">
                        Import CSV
                        <input type="file" name="file" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>

                <!-- Export -->
                <a href="{{ route('admin.users.roles.export') }}"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded-md">
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Bulk assign -->
        <div class="flex items-center gap-3 mb-4">
            <select x-model="bulkRoleId" class="border-gray-300 rounded-md text-sm px-2 py-1.5 focus:ring-blue-300">
                <option value="">Chọn vai trò...</option>
                <template x-for="r in roles" :key="r.id">
                    <option :value="r.id" x-text="r.name"></option>
                </template>
            </select>

            <button @click="bulkAssign" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded-md">
                Gán hàng loạt
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border rounded-lg shadow-sm">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left"><input type="checkbox" @change="toggleAllUsers($event)" /></th>
                        <th class="px-4 py-2">Tên</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Vai trò</th>
                        <th class="px-4 py-2">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in users" :key="user.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><input type="checkbox" x-model="selectedUsers" :value="user.id"></td>
                            <td class="px-4 py-2 font-semibold" x-text="user.name"></td>
                            <td class="px-4 py-2 text-gray-600" x-text="user.email"></td>
                            <td class="px-4 py-2">
                                <template x-for="r in user.roles" :key="r.id">
                                    <span class="px-2 py-0.5 rounded-full text-xs text-white mr-1"
                                        :class="badgeColor(r.name)" x-text="r.name"></span>
                                </template>
                            </td>
                            <td class="px-4 py-2">
                                <select multiple @change="updateRoles(user.id, $event)" x-model="user.selected_roles"
                                    class="border-gray-300 rounded-md text-xs w-full focus:ring-blue-300">
                                    <template x-for="r in roles" :key="r.id">
                                        <option :value="r.id" x-text="r.name"></option>
                                    </template>
                                </select>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div x-show="loading" x-transition
            class="fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-md shadow-md">
            <svg class="animate-spin inline w-4 h-4 mr-1" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                <path class="opacity-75" fill="white"
                    d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
            </svg>
            <span>Đang cập nhật...</span>
        </div>
    </div>

    <script>
        function userRoleManager() {
            return {
                users: {!! $usersJson !!},
                roles: {!! $rolesJson !!},
                filterRole: '{{ $filterRole ?? '' }}',
                selectedUsers: [],
                bulkRoleId: '',
                loading: false,

                applyFilter() {
                    const url = new URL(window.location.href);
                    if (this.filterRole) url.searchParams.set('role', this.filterRole);
                    else url.searchParams.delete('role');
                    window.location.href = url.toString();
                },

                badgeColor(role) {
                    const map = {
                        'Admin': 'bg-red-600',
                        'Manager': 'bg-blue-600',
                        'Staff': 'bg-green-600',
                    };
                    return map[role] || 'bg-gray-500';
                },

                updateRoles(userId, event) {
                    const ids = Array.from(event.target.selectedOptions).map(o => o.value);
                    this.loading = true;
                    fetch(`/admin/users/${userId}/roles`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ role_ids: ids })
                    }).then(() => this.loading = false);
                },

                toggleAllUsers(e) {
                    if (e.target.checked)
                        this.selectedUsers = this.users.map(u => u.id);
                    else
                        this.selectedUsers = [];
                },

                bulkAssign() {
                    if (!this.bulkRoleId || this.selectedUsers.length === 0) return alert('Chọn vai trò và người dùng!');
                    this.loading = true;
                    fetch(`{{ route('admin.users.roles.bulk') }}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ user_ids: this.selectedUsers, role_id: this.bulkRoleId })
                    }).then(() => location.reload());
                }
            }
        }
    </script>

</x-app-layout>
