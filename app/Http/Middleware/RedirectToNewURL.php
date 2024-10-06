<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Http;

use Closure;

class RedirectToNewURL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		
		if (str_contains($request->getRequestUri(), 'check_time.php')) {

        //Remove .php from the request url
        $url = str_replace('check_time.php', 'check_time', $request->url());

        foreach ($request->input() as $key => $value) {

            $url .= "/{$key}/{$value}";
        }

			return redirect($url);
		}
		else if (str_contains($request->getRequestUri(), 'http_post_server.php')) {

        //Remove .php from the request url
        $url = str_replace('http_post_server.php', 'http_post_server', $request->url());

        foreach ($request->input() as $key => $value) {

            $url .= "/{$key}?{$value}";
        }

			return redirect($url);
		}
		
		//else if (str_contains($request->getRequestUri(), 'rtu/index.php/rtu/rtu_check_update')) {

		//	echo "CSVssss";
        //Remove .php from the request url
       // $url = str_replace('rtu/index.php/rtu/rtu_check_update', 'csv_status', $request->url());

       // foreach ($request->input() as $key => $value) {

      //      $url .= "/{$key}/{$value}";
      //  }
		
	//	$response = Http::get($url);
		 
		//}
		

        return $next($request);
    }
}
