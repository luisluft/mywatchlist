@extends('layouts.app')

@section('content')
    <div id="app">
        {{--Display authorization steps only if user has not yet authorized--}}
        @if(!$user->access_token)
            <div class="row justify-content-center">
                <h2 class="col-sm-12 text-center"> Authorize Account Creation</h2>
                <button class="btn btn-primary col-6 m-1" data-toggle="modal" data-target="newProfile" v-on:click="createRequestToken" autofocus>
                    Step 1
                </button>
                <button class="btn btn-primary col-6 m-1" data-toggle="modal" data-target="newProfile" v-on:click="fetchAccessToken" autofocus>
                    Step 2
                </button>
            </div>
        @endif

    <!--Display normal page once user has access token-->
        @if($user->access_token)
        <!-- Button to Open the Modal -->
            <div class="row justify-content-center">
                <button id="newProfileButton" type="button" class="btn btn-secondary btn-block col-10 mb-1" v-on:click="openModal" autofocus>
                    New Profile
                </button>
            </div>

            @foreach($profiles as $profile)
                <div class="row justify-content-center">
                    <a class="btn btn-primary btn-block m-1 col-8" type="button" href="/profile/{{$profile->id}}">{{ $profile->title }}</a>
                    <button id="deleteProfile" type="button" class="btn btn-danger btn-block col-2 m-1" v-on:click="deleteList({{$profile->list_id}})">
                        Delete Profile
                    </button>
                </div>
            @endforeach

        <!-- Modal start -->
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
                                    <input v-model="profile_title" name="name" type="text" class="form-control" id="name" required>
                                </div>
                            </div>
                            <!--Input end -->
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" v-on:click="addList">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal end -->
        @endif

    </div>
@endsection

@section('javascript')
    <script>
        new Vue({
            el:      '#app',
            data:    {
                profile_title:       '',
                authorizationLoaded: true,
                readAccessToken:     '{{ env('TMDB_READ_ACCESS_TOKEN') }}',
                appUrl:              '{{ env('APP_URL') }}' + '/home',
                request_token:       '',
                access_token:        '{{ $user->access_token }}',
                api_base_url:        '{{ env('TMDB_BASE_URL') }}',
            },
            methods: {
                deleteProfile:      function (list_id) {
                    // delete profile from local database
                    axios({
                        method: 'delete',
                        url:    '{{ route('profile.delete') }}',
                        data:   {
                            profile_id: list_id,
                        }
                    }).then(function (response) {
                        console.log(response);
                        document.location.reload(true);
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                deleteList:         function (list_id) {
                    let vm = this;
                    axios({
                        method:  'delete',
                        url:     this.api_base_url + '/list/' + list_id,
                        headers: {
                            'content-type':  'application/json;charset=utf-8',
                            'authorization': 'Bearer ' + this.access_token,
                        },
                        data:    {
                            list_id: list_id,
                        }
                    }).then(function (response) {
                        vm.deleteProfile(list_id);
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                addList:            function () {
                    let vm = this;
                    axios({
                        method:  'post',
                        url:     vm.api_base_url + '/list',
                        data:    {
                            "name":      vm.profile_title,
                            "iso_639_1": "en",
                            public:      false,
                        },
                        headers: {
                            'content-type':  'application/json;charset=utf-8',
                            'authorization': 'Bearer ' + vm.access_token,
                        },
                    }).then(function (response) {
                        //    saves the new list to the database table profiles
                        let list_id = response.data.id;
                        axios({
                            method: 'post',
                            url:    '{{ route('profile.store') }}',
                            data:   {
                                list_id:       list_id,
                                profile_title: vm.profile_title,
                            }
                        }).then(function (response) {
                            document.location.reload(true);
                        }).catch(function (error) {
                            console.log("could not send post to profile with error = " + error);
                        });
                    }).catch(function (error) {
                        console.log(error);
                        console.log(error.data.status_message);
                    });
                },
                openModal:          function () {
                    $('#newProfile').modal('show');
                },
                createRequestToken: function () {
                    vm = this;
                    this.authorizationLoaded = false;
                    // 1. Create new request token
                    axios({
                        method:          'post',
                        url:             vm.api_base_url + '/auth/request_token',
                        withCredentials: false,
                        headers:         {
                            'Content-Type':  "application/json;charset=utf-8",
                            'Authorization': "Bearer " + this.readAccessToken,
                        },
                    }).then(function (response) {
                        // 2. Send the user to TMDb asking the user to approve the token
                        console.log(response.data.request_token);
                        vm.request_token = response.data.request_token;
                        window.open('https://www.themoviedb.org/auth/access?request_token=' + response.data.request_token);
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                fetchAccessToken:   function () {
                    let vm = this;
                    axios({
                        method:          'post',
                        url:             vm.api_base_url + '/auth/access_token',
                        data:            {
                            request_token: this.request_token,
                        },
                        withCredentials: false,
                        headers:         {
                            'Content-Type':  "application/json;charset=utf-8",
                            'Authorization': "Bearer " + this.readAccessToken,
                        },
                    }).then(function (response) {
                        let access_token = (response.data.access_token);
                        axios({
                            method: 'post',
                            url:    vm.appUrl + '/access_token',
                            data:   {
                                access_token: access_token,
                            },
                        }).then(function (response) {
                            console.log(response.data);
                            document.location.reload(true);
                        });
                    }).catch(function (error) {
                        console.log('Could not save access token, error= ');
                        alert(error);
                    });
                },
            },
        });
    </script>
@endsection
