<?php

namespace furbo\rentmanforcraft\models;

use Craft;
use craft\base\Model;

/**
 * Rentman for Craft settings
 */
class Settings extends Model
{

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $cpTitle = 'Rentman';

    public $apiUrl = 'https://api.rentman.net/';

    public $apiKey = '';

    public $productRoutes = [];

    public $categoryRoutes = [];

    public $projectRoutes = [];

    public $autoSubmitProjects = true;

    public $shootingDaysFactor = [['days' => 1, 'factor' => 1],
                                    ['days' => 2, 'factor' => 1.9],
                                    ['days' => 3, 'factor' => 2.7],
                                    ['days' => 4, 'factor' => 3.4],
                                    ['days' => 5, 'factor' => 4],
                                    ['days' => 6, 'factor' => 5],
                                    ['days' => 7, 'factor' => 6],
                                    ['days' => 8, 'factor' => 7],
                                    ['days' => 9, 'factor' => 7.5],
                                    ['days' => 10, 'factor' => 8],
                                    ['days' => 11, 'factor' => 8.5],
                                    ['days' => 12, 'factor' => 9],
                                    ['days' => 13, 'factor' => 9.5],
                                    ['days' => 14, 'factor' => 10],
                                    ['days' => 15, 'factor' => 10.5],
                                    ['days' => 16, 'factor' => 11],
                                    ['days' => 17, 'factor' => 11.5],
                                    ['days' => 18, 'factor' => 12],
                                    ['days' => 19, 'factor' => 12.5],
                                    ['days' => 20, 'factor' => 13],
                                    ['days' => 21, 'factor' => 13.5],
                                    ['days' => 22, 'factor' => 14],
                                    ['days' => 23, 'factor' => 14.5],
                                    ['days' => 24, 'factor' => 15],
                                    ['days' => 25, 'factor' => 15.5],
                                    ['days' => 26, 'factor' => 16],
                                    ['days' => 27, 'factor' => 16.5],
                                    ['days' => 28, 'factor' => 17],
                                    ['days' => 29, 'factor' => 17.5],
                                    ['days' => 30, 'factor' => 18],
                                    ['days' => 31, 'factor' => 18.5],
                                    ['days' => 32, 'factor' => 19],
                                    ['days' => 33, 'factor' => 19.5],
                                    ['days' => 34, 'factor' => 20],
                                    ['days' => 35, 'factor' => 20.5],
                                    ['days' => 36, 'factor' => 21],
                                    ['days' => 37, 'factor' => 21.5],
                                    ['days' => 38, 'factor' => 22],
                                    ['days' => 39, 'factor' => 22.5],
                                    ['days' => 40, 'factor' => 23],
                                    ['days' => 41, 'factor' => 23.5],
                                    ['days' => 42, 'factor' => 24],
                                    ['days' => 43, 'factor' => 24.5],
                                    ['days' => 44, 'factor' => 25],
                                    ['days' => 45, 'factor' => 25.5],
                                    ['days' => 46, 'factor' => 26],
                                    ['days' => 47, 'factor' => 26.5],
                                    ['days' => 48, 'factor' => 27],
                                    ['days' => 49, 'factor' => 27.5],
                                    ['days' => 50, 'factor' => 28],
                                    ['days' => 51, 'factor' => 28.5],
                                    ['days' => 52, 'factor' => 29],
                                    ['days' => 53, 'factor' => 29.5],
                                    ['days' => 54, 'factor' => 30],
                                    ['days' => 55, 'factor' => 30.5],
                                    ['days' => 56, 'factor' => 31],
                                    ['days' => 57, 'factor' => 31.5],
                                    ['days' => 58, 'factor' => 32],
                                    ['days' => 59, 'factor' => 32.5],
                                    ['days' => 60, 'factor' => 33],
                                    ['days' => 61, 'factor' => 33.5],
                                    ['days' => 62, 'factor' => 34],
                                    ['days' => 63, 'factor' => 34.5],
                                    ['days' => 64, 'factor' => 35],
                                    ['days' => 65, 'factor' => 35.5],
                                    ['days' => 66, 'factor' => 36],
                                    ['days' => 67, 'factor' => 36.25],
                                    ['days' => 68, 'factor' => 36.5],
                                    ['days' => 69, 'factor' => 36.75],
                                    ['days' => 70, 'factor' => 37],
                                    ['days' => 71, 'factor' => 37.25],
                                    ['days' => 72, 'factor' => 37.5],
                                    ['days' => 73, 'factor' => 37.75],
                                    ['days' => 74, 'factor' => 38],
                                    ['days' => 75, 'factor' => 38.25],
                                    ['days' => 76, 'factor' => 38.5],
                                    ['days' => 77, 'factor' => 38.75],
                                    ['days' => 78, 'factor' => 39],
                                    ['days' => 79, 'factor' => 39.25],
                                    ['days' => 80, 'factor' => 39.5],
                                    ['days' => 81, 'factor' => 39.75],
                                    ['days' => 82, 'factor' => 40],
                                    ['days' => 83, 'factor' => 40.25],
                                    ['days' => 84, 'factor' => 40.5],
                                    ['days' => 85, 'factor' => 40.75],
                                    ['days' => 86, 'factor' => 41],
                                    ['days' => 87, 'factor' => 41.25],
                                    ['days' => 88, 'factor' => 41.5],
                                    ['days' => 89, 'factor' => 41.75],
                                    ['days' => 90, 'factor' => 42],
                                    ['days' => 91, 'factor' => 42.25],
                                    ['days' => 92, 'factor' => 42.5],
                                    ['days' => 93, 'factor' => 42.75],
                                    ['days' => 94, 'factor' => 43],
                                    ['days' => 95, 'factor' => 43.25],
                                    ['days' => 96, 'factor' => 43.5],
                                    ['days' => 97, 'factor' => 43.75],
                                    ['days' => 98, 'factor' => 44],
                                    ['days' => 99, 'factor' => 44.25],
                                    ['days' => 100, 'factor' => 44.5],
                                    ['days' => 101, 'factor' => 44.75],
                                    ['days' => 102, 'factor' => 45],
                                    ['days' => 103, 'factor' => 45.25],
                                    ['days' => 104, 'factor' => 45.5],
                                    ['days' => 105, 'factor' => 45.75],
                                    ['days' => 106, 'factor' => 46],
                                    ['days' => 107, 'factor' => 46.25]
                                ];
    public $pdfFilename = '';
    public $templateForProjectPdf = [];
    public $projectPdfFooter = '';
    public $projectEmailSubject = '';
    public $templateForProjectEmail = [];



}
