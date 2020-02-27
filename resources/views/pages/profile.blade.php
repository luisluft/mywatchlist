@extends('layouts.app')

@section('content')
    <div id="scope" class="container-xl">
        <div class="row justify-content-center">
            <!-- Button to Open the Modal -->
            <input v-model=queryString type="text" class="col-sm-9">
            <button id="searchMovie" type="button" class="btn btn-primary col-sm-3" v-on:click="searchMovie" autofocus>
                Search
            </button>
        </div>

        <!--displays all movies already added to list-->
        <div class="row justify-content-start mt-2">
            <div class="card col-sm-3" v-for="(item, index) in user_list.results">
                <img class="card-img-top mt-2" :src="imageUrl + item.poster_path" alt="image not found">
                <button id="removeFromList" class="btn btn-danger" v-on:click="removeFromList(item.id, index); fetchList();">
                    Remove
                </button>
                <div class="card-body">
                    <h5 class="card-title">@{{ item.original_title }}</h5>
                    <p class="card-text">@{{ item.overview }}</p>
                </div>
            </div>
        </div>

        <!-- modal start -->
        <div class="modal" id="search">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Results for "@{{ queryString }}"</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body row">
                        <div class="card col-sm-4" v-for="(item, index) in items.results">
                            <img class="card-img-top mt-2" :src="imageUrl + item.poster_path" alt="">
                            <button id="addToList" type="button" class="btn btn-primary" v-on:click="addToList(item.id, index); fetchList();">
                                Watchlater
                            </button>
                            <div class="card-body">
                                <h5 class="card-title">@{{ item.original_title }}</h5>
                                <p class="card-text text-justify">@{{ item.overview }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal end -->
    </div>
@endsection
@section('javascript')
    <script>
        new Vue({
            el:      '#scope',
            data:    {
                access_token: '{{ $user->access_token }}',
                profileName:  '',
                queryString:  '',
                items:        [],
                user_list:    [],
                imageUrl:     'https://image.tmdb.org/t/p/w500',
                baseUrl:      'https://api.themoviedb.org/4',
                apiKey:       '{{ env('TMDB_API_KEY') }}',
                profile_id:   '{{ $profile->id }}',
                user_id:      '{{ $user->id }}',
                list_id:      '{{ $profile->list_id }}',
            },
            mounted: function () {
                // everytime the page is reloaded the list of movies will be fetched
                this.fetchList();
            },
            methods: {
                fetchList:      function () {
                    // Make a request for a user with a given ID
                    let vm = this;
                    // TODO get all pages from the request by reading the amount of pages fetched with the response
                    axios({
                        method:  'get',
                        url:     this.baseUrl + '/list/' + this.list_id + '?page=1&api_key=' + this.apiKey + '&sort_by=original_order.desc',
                        headers: {
                            'content-type':  'application/json;charset=utf-8',
                            'authorization': 'Bearer ' + vm.access_token,
                        },
                    }).then(function (response) {
                        // handle success
                        vm.user_list = response.data;
                    }).catch(function (error) {
                        // handle error
                        console.log(error);
                    });
                },
                searchMovie:    function () {
                    $('#search').modal('show');
                    let vm = this;
                    let url = this.baseUrl + '/search/movie?api_key=' + this.apiKey + '&language=en-US&query=' + this.queryString + '&include_adult=false';

                    // make the request
                    axios.get(url)
                        .then(function (response) {
                            // handle success
                            vm.items = response.data;
                        })
                        .catch(function (error) {
                            // handle error
                            console.log(error);
                        });
                },
                addToList:      function (mediaId, index) {
                    let vm = this;
                    axios({
                        method:  'post',
                        url:     this.baseUrl + '/list/' + this.list_id + '/items',
                        headers: {
                            'content-type':  'application/json;charset=utf-8',
                            'authorization': 'Bearer ' + vm.access_token,
                        },
                        data:    {
                            "items": [
                                {
                                    "media_type": "movie",
                                    "media_id":   mediaId
                                }
                            ]
                        }
                    }).then(function (response) {
                        // removes the movie added from the search results frontend
                        vm.items.results.splice(index, 1);
                        vm.fetchList();
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                removeFromList: function (mediaId, index) {
                    // removes the movie added to watchlater from the search results frontend
                    let vm = this;
                    axios({
                        method: 'delete',
                        url:    this.baseUrl + '/list/' + this.list_id + '/items',
                        headers: {
                            'content-type':  'application/json;charset=utf-8',
                            'authorization': 'Bearer ' + vm.access_token,
                        },
                        data:   {
                            "items": [
                                {
                                    "media_type": "movie",
                                    "media_id":   mediaId
                                }
                            ]
                        }
                    }).then(function (response) {
                        vm.fetchList();
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
            },
        });

    </script>
@endsection
