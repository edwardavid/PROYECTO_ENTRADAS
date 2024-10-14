<?php

require 'vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

// Función principal que recibe una URL

function analizarEntradas($url) {

    
    
    $client = HttpClient::create([
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0',  // Simula un navegador real
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Connection' => 'keep-alive'
        ]
    ]);
    
    
    if (strpos($url, 'vividseats.com') !== false) {
        analizarVividSeats($client, $url);
    } elseif (strpos($url, 'seatgeek.com') !== false) {
        analizarSeatGeek($client, $url);
    } else {
        echo "URL no soportada. Solo se soportan VividSeats y SeatGeek.\n";
    }
}

// Función para analizar entradas de VividSeats

function analizarVividSeats($client, $url) {
    try {
        // Realizamos la solicitud y obtenemos el contenido

        $response = $client->request('GET', $url);
        $html = $response->getContent();

        // Creamos un nuevo Crawler con el contenido HTML

        $crawler = new Crawler($html);

        // Extraemos los sectores, filas y precios

        $entradas = $crawler->filter('.listing-ticket'); // Selector CSS
        if ($entradas->count() > 0) {
            echo "Entradas disponibles en VividSeats:\n";
            $entradas->each(function ($entrada) {

               

                $sector = $entrada->filter('.ticket-listing-section')->count() > 0 ? $entrada->filter('.ticket-listing-section')->text() : 'N/A';
                $fila = $entrada->filter('.ticket-listing-row')->count() > 0 ? $entrada->filter('.ticket-listing-row')->text() : 'N/A';
                $precio = $entrada->filter('.ticket-listing-price')->count() > 0 ? $entrada->filter('.ticket-listing-price')->text() : 'N/A';
                echo "Sector: $sector, Fila: $fila, Precio: $precio\n";
            });
        } else {
            echo "No se encontraron entradas en VividSeats.\n";
        }
    } catch (\Exception $e) {
        echo "Hubo un error al procesar la página de VividSeats: " . $e->getMessage() . "\n";
    }
}



// Función para analizar entradas de SeatGeek

function analizarSeatGeek($client, $url) {
    try {

        // Realizamos la solicitud y obtenemos el contenido

        $response = $client->request('GET', $url);
        $html = $response->getContent();

        // Creamos un nuevo Crawler con el contenido HTML

        $crawler = new Crawler($html);






        // Extraemos los sectores, filas y precios

        $entradas = $crawler->filter('.listing-ticket'); // Selector CSS aproximado
        if ($entradas->count() > 0) {
            echo "Entradas disponibles en SeatGeek:\n";
            $entradas->each(function ($entrada) {

                // Agregamos comprobaciones para evitar errores si algún elemento no existe

                $sector = $entrada->filter('.ticket-listing-section')->count() > 0 ? $entrada->filter('.ticket-listing-section')->text() : 'N/A';
                $fila = $entrada->filter('.ticket-listing-row')->count() > 0 ? $entrada->filter('.ticket-listing-row')->text() : 'N/A';
                $precio = $entrada->filter('.ticket-listing-price')->count() > 0 ? $entrada->filter('.ticket-listing-price')->text() : 'N/A';
                echo "Sector: $sector, Fila: $fila, Precio: $precio\n";
            });
        } else {
            echo "No se encontraron entradas en SeatGeek.\n";
        }
    } catch (\Exception $e) {
        echo "Hubo un error al procesar la página de SeatGeek: " . $e->getMessage() . "\n";
    }
}




// Obtenemos la URL desde los parámetros GET de la solicitud

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    analizarEntradas($url);
} else {
    echo "Por favor proporciona una URL de evento como parámetro 'url'. Ejemplos:\n";
    echo "VividSeats: http://localhost:8000/analizador_entradas.php?url=https://www.vividseats.com/real-madrid-tickets-estadio-santiago-bernabeu-12-22-2024--sports-soccer/production/5045935\n";
    echo "SeatGeek: http://localhost:8000/analizador_entradas.php?url=https://seatgeek.com/taylor-swift-tickets/toronto-canada-rogers-centre-2024-11-15-7-pm/concert/6109452\n";
}
