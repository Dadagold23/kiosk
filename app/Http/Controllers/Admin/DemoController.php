<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class DemoController extends Controller
{
    public function show($page)
    {
        $filePath = public_path("demo-html/{$page}.html");
        
        if (!File::exists($filePath)) {
            $filePath = public_path("demo-html/{$page}");
            if (!File::exists($filePath)) {
                abort(404, "Demo page not found.");
            }
        }
        
        $html = File::get($filePath);
        
        // Extract content inside <div class="page-content"> ... </div>
        $startTag = '<div class="page-content">';
        $startPos = strpos($html, $startTag);
        
        if ($startPos === false) {
            $startTag = '<div class="page-wrapper">';
            $startPos = strpos($html, $startTag);
        }
        
        if ($startPos === false) {
            $content = $html;
        } else {
            $contentStart = $startPos + strlen($startTag);
            
            // Find matching closing </div>
            $openDivs = 1;
            $currentIndex = $contentStart;
            $htmlLength = strlen($html);
            $contentEnd = null;
            
            while ($openDivs > 0 && $currentIndex < $htmlLength) {
                $nextOpen = strpos($html, '<div', $currentIndex);
                $nextClose = strpos($html, '</div', $currentIndex);
                
                if ($nextClose === false) {
                    break;
                }
                
                if ($nextOpen !== false && $nextOpen < $nextClose) {
                    $openDivs++;
                    $currentIndex = $nextOpen + 4;
                } else {
                    $openDivs--;
                    if ($openDivs === 0) {
                        $contentEnd = $nextClose;
                        break;
                    }
                    $currentIndex = $nextClose + 5;
                }
            }
            
            if ($contentEnd !== null) {
                $content = substr($html, $contentStart, $contentEnd - $contentStart);
            } else {
                $content = substr($html, $contentStart);
            }
        }
        
        // Extract page title from <title>
        $pageTitle = 'Demo Page';
        if (preg_match('/<title>(.*?)<\/title>/i', $html, $matches)) {
            $pageTitle = $matches[1];
        }
        
        // Extract page-specific styles, excluding core bundle css
        $pageStyles = [];
        if (preg_match_all('/<link[^>]*href=["\'](?!.*(bootstrap|app\.css|icons\.css|semi-dark|header-colors|pace|simplebar|perfect-scrollbar|metisMenu|roboto|bootstrap-icons))(.*?)["\'][^>]*>/i', $html, $linkMatches)) {
            foreach ($linkMatches[0] as $linkTag) {
                $linkTag = preg_replace('/href=["\']assets\/(.*?)["\']/i', 'href="/demo/assets/$1"', $linkTag);
                $pageStyles[] = $linkTag;
            }
        }
        
        // Extract page-specific script files, excluding core bundle scripts
        $pageScripts = [];
        if (preg_match_all('/<script[^>]*src=["\'](?!.*(bootstrap|jquery|simplebar|metisMenu|perfect-scrollbar|app\.js|pace))(.*?)["\'][^>]*><\/script>/i', $html, $scriptMatches)) {
            foreach ($scriptMatches[0] as $scriptTag) {
                $scriptTag = preg_replace('/src=["\']assets\/(.*?)["\']/i', 'src="/demo/assets/$1"', $scriptTag);
                $pageScripts[] = $scriptTag;
            }
        }
        
        // Extract page-specific inline script blocks (like charts configuration)
        if (preg_match_all('/<script\b[^>]*>(?!.*secureserver\.net)(.*?)<\/script>/is', $html, $inlineMatches)) {
            foreach ($inlineMatches[0] as $scriptTag) {
                if (strpos($scriptTag, 'src=') !== false) {
                    continue;
                }
                if (strpos($scriptTag, 'secureserver.net') !== false || strpos($scriptTag, '_trfq') !== false) {
                    continue;
                }
                $pageScripts[] = $scriptTag;
            }
        }
        
        // Rewrite static asset paths inside content
        $content = preg_replace('/src=["\']assets\/(.*?)["\']/i', 'src="/demo/assets/$1"', $content);
        $content = preg_replace('/href=["\']assets\/(.*?)["\']/i', 'href="/demo/assets/$1"', $content);
        
        // Rewrite navigation links pointing to other .html pages
        $content = preg_replace('/href=["\'](?!http|https|mailto|javascript|\/|#)(.*?)\.html["\']/i', 'href="/admin/demo/$1.html"', $content);

        return view('admin.demo-wrapper', [
            'content' => $content,
            'pageTitle' => $pageTitle,
            'page' => $page,
            'styles' => $pageStyles,
            'scripts' => $pageScripts
        ]);
    }
}
