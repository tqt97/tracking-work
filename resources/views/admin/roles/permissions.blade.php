<x-app-layout>

    <div class="p-6 max-w-6xl mx-auto" x-data="rolePermissionMatrix()">
        <h1 class="text-2xl font-bold mb-4">Quản lý quyền cho vai trò</h1>

        <!-- Filter + Loading -->
        <div class="flex items-center justify-between mb-4">
            <input type="text" x-model="search" placeholder="Tìm quyền..."
                class="border-gray-300 rounded-md w-1/3 px-3 py-2 text-sm focus:ring focus:ring-blue-200">

            <div class="flex items-center gap-2" x-show="loading" x-transition>
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z">
                    </path>
                </svg>
                <span class="text-sm text-gray-500">Đang cập nhật...</span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border rounded-lg shadow-sm">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="px-3 py-2 text-left">Quyền</th>
                        <template x-for="role in roles" :key="role.id">
                            <th class="px-3 py-2 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span x-text="role.name"></span>
                                    <!-- Bulk Select -->
                                    <input type="checkbox" :checked="isAllSelected(role.id)"
                                        @change="toggleAll(role.id, $event.target.checked)"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-[10px] text-gray-500">Tất cả</span>
                                </div>
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="perm in filteredPermissions()" :key="perm.id">
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-700" x-text="perm.name"></td>
                            <template x-for="role in roles" :key="role.id">
                                <td class="px-3 py-2 text-center">
                                    <label class="cursor-pointer inline-flex items-center justify-center w-full">
                                        <input type="checkbox" :checked="roleHasPermission(role.id, perm.id)"
                                            @change="togglePermission(role.id, perm.id, $event.target.checked)"
                                            class="hidden">
                                        <div :class="checkboxStyle(role.id, perm.id)"
                                            class="w-5 h-5 rounded border border-gray-300 flex items-center justify-center transition">
                                            <template x-if="roleHasPermission(role.id, perm.id)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 9.707a1 1 0 011.414-1.414L8 11.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </template>
                                        </div>
                                    </label>
                                </td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-gray-500 text-sm">
            <p><strong>Mẹo:</strong> Tick “Tất cả” để chọn toàn bộ quyền cho vai trò. Gõ vào ô tìm kiếm để lọc quyền
                nhanh.</p>
        </div>
    </div>

    <script>
        function rolePermissionMatrix() {
            return {
                roles: @json($roles),
                permissions: @json($permissions),
                search: '',
                loading: false,

                filteredPermissions() {
                    if (!this.search) return this.permissions;
                    return this.permissions.filter(p =>
                        p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.slug.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                roleHasPermission(roleId, permId) {
                    const role = this.roles.find(r => r.id === roleId);
                    return role && role.permissions.find(p => p.id === permId);
                },

                checkboxStyle(roleId, permId) {
                    return this.roleHasPermission(roleId, permId)
                        ? 'bg-blue-100 border-blue-400 hover:bg-blue-200'
                        : 'bg-gray-100 hover:bg-gray-200';
                },

                // 🌀 Bulk check: kiểm tra xem role có đủ tất cả permission hay không
                isAllSelected(roleId) {
                    const role = this.roles.find(r => r.id === roleId);
                    if (!role) return false;
                    return role.permissions.length === this.permissions.length;
                },

                // ✅ Bulk action: chọn/bỏ chọn toàn bộ
                toggleAll(roleId, checked) {
                    const role = this.roles.find(r => r.id === roleId);
                    if (!role) return;

                    this.loading = true;

                    const ids = checked ? this.permissions.map(p => p.id) : [];

                    fetch(`/api/roles/${roleId}/sync-permissions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ permission_ids: ids })
                    })
                        .then(res => res.json())
                        .then(() => {
                            role.permissions = this.permissions.filter(p => ids.includes(p.id));
                        })
                        .finally(() => this.loading = false);
                },

                togglePermission(roleId, permId, checked) {
                    const role = this.roles.find(r => r.id === roleId);
                    if (!role) return;

                    let ids = role.permissions.map(p => p.id);
                    if (checked) {
                        ids.push(permId);
                    } else {
                        ids = ids.filter(id => id !== permId);
                    }

                    this.loading = true;

                    fetch(`/api/roles/${roleId}/sync-permissions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ permission_ids: ids })
                    })
                        .then(res => res.json())
                        .then(() => {
                            role.permissions = this.permissions.filter(p => ids.includes(p.id));
                        })
                        .finally(() => this.loading = false);
                }
            }
        }
    </script>
</x-app-layout>
