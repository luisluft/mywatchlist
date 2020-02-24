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

            @foreach($movies as $movie)
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body border text-center">
                            {{$movie->title}}
                        </div>
                    </div>
                </div>
        @endforeach

            <!-- Create client modal start -->
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

                                <div class="card col-md-4" v-for="item in items.results">
                                    <img class="card-img-top" :src="imageUrl + item.poster_path" alt="">
                                    <a href="#" class="btn btn-primary">Watchlater</a>
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
            <!-- Create client modal end -->

        </div>
    </div>
</div>
@endsection
@section('javascript')
    <script>
        new Vue({
            el:      '#scope',
            data:    {
                profileName:     '',
                queryString:'',
                loaded:   true,
                items:    [],
                imageUrl: 'https://image.tmdb.org/t/p/w342',


            },
            methods: {
                searchTMDB: function () {
                    $('#newProfile').modal('show');
                    let vm = this;
                    let url = 'https://api.themoviedb.org/3/search/movie?api_key='+ '{{env('TMDB_API_KEY')}}' + '&language=en-US&query=' + this.queryString + '&include_adult=true';
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
            },
        });

    </script>
@endsection
