<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;

/**
 * Base controller for the admin panel. Empty subclass that lets every
 * Admin\* controller share a single parent without polluting the global
 * App\Http\Controllers\Controller.
 */
abstract class Controller extends BaseController
{
}
