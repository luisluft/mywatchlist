@extends('layouts.app')

@section('content')
    <div id="scope">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Button to Open the Modal -->
                <input v-model=queryString type="text" class="col-sm-8">
                <button id="searchTMDB" type="button" class="btn btn-primary col-sm-2" data-toggle="modal" data-target="newProfile" v-on:click="searchTMDB" autofocus>
                    Search
                </button>

                <!-- modal start -->
                <div class="modal" id="newProfile">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content modal-xl">
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
                                        <button id="addWatchlater" type="button" class="btn btn-primary" v-on:click="addWatchlater(item.id, index)">Watchlater
                                        </button>
                                        <div class="card-body">
                                            <h5 class="card-title">@{{ item.original_title }}</h5>
                                            <p class="card-text">@{{ item.overview }}</p>
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
            el:      '#scope',
            data:    {
                profileName: '',
                queryString: '',
                loaded:      true,
                items:       [],
                imageUrl:    'https://image.tmdb.org/t/p/w342',
            },
            methods: {
                searchTMDB:    function () {
                    $('#newProfile').modal('show');
                    let vm = this;
                    let url = 'https://api.themoviedb.org/3/search/movie?api_key=' + '{{env('TMDB_API_KEY')}}' + '&language=en-US&query=' + this.queryString + '&include_adult=false';
                    console.log(url);

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
                addWatchlater: function (mediaId, index) {
                    let profile_id = window.location.pathname.split('/')[2];
                    // removes the movie added to watchlater from the search results
                    this.items.results.splice(index, 1);
                    axios.post('/profile/session', {
                        profile_id: profile_id,
                    }).then(function (response) {
                        let session_id = response.data.session_id;
                        let user_id = response.data.user_id;
                        let url = 'https://api.themoviedb.org/3/account/' + user_id + '/watchlist?api_key=' + '{{ env('TMDB_API_KEY') }}' + '&session_id=' + session_id;
                        console.log(url);
                        axios.post(url, {
                            "media_type": "movie",
                            "media_id":   mediaId,
                            "watchlist":  true
                        }).then(function (response) {
                            // this.items.splice(index,1);
                        })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }).catch(function (error) {
                        console.log(error);
                    });
                }
            },
        });

    </script>
@endsection
