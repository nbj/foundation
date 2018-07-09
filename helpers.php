<?php


if (!function_exists('app')) {
    /**
     * Helper function to the get application object
     *
     * @return \Nbj\Foundation\Application
     */
    function app() {
        return \Nbj\Foundation\Application::getInstance();
    }
}
