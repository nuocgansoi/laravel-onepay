<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 10/31/2017
 * Time: 2:06 PM
 */

/**
 * @return \Illuminate\Foundation\Application|NuocGanSoi\LaravelOnepay\Helpers\OnepayHelper
 */
function onepay_helper()
{
    return app(\NuocGanSoi\LaravelOnepay\Helpers\OnepayHelper::class);
}
