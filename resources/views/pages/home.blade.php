@extends('layouts.app')

@section('content')
    <div class="container" id="scope">
        <!-- Button to Open the Modal -->
        <button id="newProfileButton" type="button" class="btn btn-secondary btn-block" data-toggle="modal" data-target="newProfile" v-on:click="openModal" autofocus>
            New Profile
        </button>

        @foreach($profiles as $profile)
            <a class="btn btn-primary btn-block" href="/profile/{{$profile->id}}" role="button">{{ $profile->title }}</a>
        @endforeach

    <!-- Create client modal start -->
        <div class="modal" id="newProfile">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">New Profile</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <!--Input start -->
                        <div class="form-group">
                            <label for="name" class="control-label">Name</label>
                            <div class="input-group">
                                <input v-model="profileName" name="name" type="text" class="form-control" id="name" required>
                            </div>
                        </div>
                        <!--Input end -->
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" v-on:click="addProfile">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Create client modal end -->
    </div>
@endsection

@section('javascript')
    <script>

        new Vue({
            el:      '#scope',
            data:    {
                profileName:     '',
            },
            methods: {
                addProfile: function () {
                    axios.post('/profiles', {
                        formData: this.profileName,
                    }).then(function (response) {
                        console.log(response.data);
                        $("#newProfile").modal('hide');
                        location.reload();
                    }).catch(function (error) {
                        alert('Profile name is invalid.');
                    });
                },
                openModal: function() {
                    $('#newProfile').modal('show');
                }
            }
        });

    </script>
@endsection
