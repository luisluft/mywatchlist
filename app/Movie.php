<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Movie
 *
 * @property int $id
 * @property int $profile_id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Movie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Movie extends Model
{
    //
}
