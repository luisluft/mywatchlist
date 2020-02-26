@extends('layouts.app')

@section('content')
    {{--Display authorization steps only if user has not yet authorized--}}
    @if(!$user->access_token)
        <div class="row justify-content-center" id="app">
            <h2 class="col-sm-12 text-center"> Authorize Account Creation</h2>
            <button class="btn btn-primary col-sm-8 m-1" data-toggle="modal" data-target="newProfile" v-on:click="createRequestToken" autofocus>
                Step 1
            </button>
            <button class="btn btn-primary col-sm-8 m-1" data-toggle="modal" data-target="newProfile" v-on:click="fetchAccessToken" autofocus>
                Step 2
            </button>
        </div>
    @endif

    @if($user->access_token)
        <h2>user has access token</h2>
    @endif

@endsection

@section('javascript')
    <script>
        new Vue({
            el:      '#app',
            data:    {
                authorizationLoaded:    true,
                readAccessToken:        '{{ env('TMDB_READ_ACCESS_TOKEN') }}',
                appUrl:                 '{{ env('APP_URL') }}' + '/home',
                authorizedRequestToken: window.location.pathname.split('/')[2],
                request_token:          '',
                access_token:           '',
            },
            created: function () {
            },
            methods: {
                createRequestToken: function () {
                    vm = this;
                    this.authorizationLoaded = false;
                    // 1. Create new request token
                    axios({
                        method:          'post',
                        url:             'https://api.themoviedb.org/4/auth/request_token',
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
                        url:             'https://api.themoviedb.org/4/auth/access_token\n',
                        data:            {
                            request_token: this.request_token,
                        },
                        withCredentials: false,
                        headers:         {
                            'Content-Type':  "application/json;charset=utf-8",
                            'Authorization': "Bearer " + this.readAccessToken,
                        },
                    }).then(function (response) {
                        vm.access_token = (response.data.access_token);
                        axios({
                            method: 'post',
                            url:    vm.appUrl + '/access_token',
                            data:   {
                                access_token: vm.access_token,
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
