@extends('layouts.app')

@section('content')
    <div id="scope">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Button to Open the Modal -->
                <input v-model=queryString type="text" class="col-sm-8">
                <button id="searchMovie" type="button" class="btn btn-primary col-sm-2" data-toggle="modal" data-target="newProfile" v-on:click="searchMovie" autofocus>
                    Search
                </button>

                <button id="fetchWatchLater" type="button" class="btn btn-primary mt-2 col-sm-12 text-center" data-toggle="modal" data-target="newProfile" v-on:click="fetchWatchLater" autofocus>
                    Show Watchlater List
                </button>

                <div v-show="loaded2" class="loader"></div>

                <div class="card col-md-4" v-for="(item, index) in movies.results">
                    <img class="card-img-top" :src="imageUrl + item.poster_path" alt="">
                    <button id="removeWatchlater" class="btn btn-danger" v-on:click="removeWatchlater(item.id, index); fetchWatchLater();">
                        Remove
                    </button>
                    <div class="card-body">
                        <h5 class="card-title">@{{ item.original_title }}</h5>
                        <p class="card-text">@{{ item.overview }}</p>
                    </div>
                </div>

                <!-- modal start -->
                <div class="modal" id="newProfile">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Results for "@{{ queryString }}"</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                <div id="scope" class="row">
                                    <div v-show="loaded" class="loader"></div>
                                    <div class="card col-md-4" v-for="(item, index) in items.results">
                                        <img class="card-img-top" :src="imageUrl + item.poster_path" alt="">
                                        <button id="addWatchlater" type="button" class="btn btn-primary" v-on:click="addWatchlater(item.id, index); fetchWatchLater();">
                                            Watchlater
                                        </button>
                                        <div class="card-body">
                                            <h5 class="card-title">@{{ item.original_title }}</h5>
                                            <p class="card-text text-justify">@{{ item.overview }}</p>
                                        </div>
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
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        new Vue({
            el:            '#scope',
            data:          {
                profileName: '',
                queryString: '',
                items:       [],
                movies:      [],
                imageUrl:    'https://image.tmdb.org/t/p/w342',
                baseUrl:     'https://api.themoviedb.org/3',
                apiKey:      '{{ env('TMDB_API_KEY') }}',
                profile_id:  window.location.pathname.split('/')[2],
                loaded:      true,
                loaded2:     true,
                user_id:     '', // 1
                session_id:  '', // 6ef084b1672a5fbdf9f931ed73fcf1c849aaa274
            },
            asyncComputed: {
                async getUserId() {
                    this.user_id = await axios.post('/profile/data', {
                        profile_id: this.profile_id,
                    }).then((response) => {
                        return response.data.user_id;
                    });
                },
                async getSessionId() {
                    this.session_id = await axios.post('/profile/data', {
                        profile_id: this.profile_id,
                    }).then((response) => {
                        return response.data.session_id;
                    });
                },
            },
            methods:       {
                // Fetch data from the API
                fetchWatchLater:  function () {
                    let vm = this;
                    let url = this.baseUrl + '/account/' + this.user_id + '/watchlist/movies?api_key=' + this.apiKey + '&language=en-US&session_id=' + '9a49fe4fa1439f6bee09996915b15c6715e959f7' + '&sort_by=created_at.desc';
                    // Make a request for a user with a given ID
                    axios.get(url).then(function (response) {
                        // handle success
                        vm.movies = response.data;
                        vm.loaded2 = false;
                    }).catch(function (error) {
                        // handle error
                        console.log(error);
                    });
                },
                searchMovie:      function () {
                    $('#newProfile').modal('show');
                    let vm = this;
                    let url = this.baseUrl + '/search/movie?api_key=' + this.apiKey + '&language=en-US&query=' + this.queryString + '&include_adult=false';

                    // make the request
                    axios.get(url)
                        .then(function (response) {
                            // handle success
                            vm.items = response.data;
                            vm.loaded = false;
                        })
                        .catch(function (error) {
                            // handle error
                            console.log(error);
                        });
                },
                addWatchlater:    function (mediaId, index) {
                    // removes the movie added to watchlater from the search results frontend
                    this.items.results.splice(index, 1);
                    let url = this.baseUrl + '/account/' + this.user_id + '/watchlist?api_key=' + this.apiKey + '&session_id=' + this.session_id;

                    axios.post(url, {
                        "media_type": "movie",
                        "media_id":   mediaId,
                        "watchlist":  true
                    }).then(function (response) {
                        // this.items.splice(index,1);
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                removeWatchlater: function (mediaId, index) {
                    // removes the movie added to watchlater from the search results frontend
                    let url = this.baseUrl + '/account/' + this.user_id + '/watchlist?api_key=' + this.apiKey + '&session_id=' + this.session_id;

                    axios.post(url, {
                        "media_type": "movie",
                        "media_id":   mediaId,
                        "watchlist":  false
                    }).then(function (response) {
                        // this.items.splice(index,1);
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
            },
        });

    </script>
@endsection
