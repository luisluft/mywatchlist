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
                profileName:       '',
            },
            methods: {
                addProfile: function () {
                    axios.post('/profile', {
                        formData: this.profileName,
                    }).then(function (response) {
                        // console.log('profile with id: ' + response.data + ' saved');
                        let profile_id = response.data;
                        $("#newProfile").modal('hide');
                        // 1. Create a new request token
                        axios.get('https://api.themoviedb.org/3/authentication/token/new?api_key=' + '{{ env('TMDB_API_KEY') }}')
                            .then(function (response) {
                                let request_token = response.data.request_token;
                                // console.log('request token: ' + request_token);
                                // 2. authorize the request token
                                axios.post('https://api.themoviedb.org/3/authentication/token/validate_with_login?api_key=' + '{{ env('TMDB_API_KEY') }}', {
                                    "username":      '{{ env('TMDB_USERNAME') }}',
                                    "password":      '{{ env('TMDB_PASSWORD') }}',
                                    "request_token": request_token,
                                })
                                    .then(function (response) {
                                        // console.log('authorized request token: ' + response.data.request_token);
                                        // 3. create new session_id with authorized token
                                        axios.post('https://api.themoviedb.org/3/authentication/session/new?api_key=' + '{{ env('TMDB_API_KEY') }}', {
                                            "request_token": request_token
                                        })
                                            .then(function (response) {
                                                // console.log(response.data.success);
                                                let session_id = response.data.session_id;
                                                // 4. save session_id to the profile table inside local database
                                                axios.put('/profile', {
                                                    "session_id": session_id,
                                                    "profile_id": profile_id,
                                                })
                                                    .then(function (response) {
                                                        // console.log(response.data);
                                                        location.reload();
                                                    })
                                                    .catch(function (error) {
                                                        console.log(error);
                                                    });
                                            })
                                            .catch(function (error) {
                                                console.log(error);
                                            });
                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                    });
                            });
                    }).catch(function (error) {
                        alert('Unable to create the profile');
                    });
                },
                openModal:  function () {
                    $('#newProfile').modal('show');
                }
            }
        });

    </script>
@endsection
