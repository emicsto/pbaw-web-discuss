<?php

namespace App\Http\ViewComposers;

use App\Category;
use Illuminate\View\View;

class CategoriesComposer
{
    /**
     * Create a new categories composer.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('categories', Category::all());
    }
}