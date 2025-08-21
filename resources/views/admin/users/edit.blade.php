<x-admin>
    <div class="container users">
        <div class="custom_form users_form">
            <h1> Update User</h1>
            <p>
                <span>Names</span>
                <span>{{ $user->first_name }} {{ $user->last_name }}</span>
            </p>
            <p>
                <span>Email</span>
                <span>{{ $user->email }}</span>
            </p>
            <p>
                <span>Phone Number</span>
                <span>{{ $user->phone_number }}</span>
            </p>

            <form action="{{ route('user.update', ['user' => $user->id]) }}" method="post">
                @csrf
                @method('PATCH')

                <div class="row_input_group">
                    <div class="input_group">
                        <label for="user_status">Status</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="user_status" id="user_status" value="1" {{ ($user->user_status == 1) ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="user_status" id="status_0" value="0" {{ ($user->user_status == 0) ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>

                    <div class="input_group">
                        <label for="user_level">User Level</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="user_level" id="user_level_1" value="1" {{ ($user->user_level == 1) ? 'checked' : '' }}>
                                <span>Admin</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="user_level" id="user_level_2" value="2" {{ ($user->user_level == 2) ? 'checked' : '' }}>
                                <span>User</span>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</x-admin>
