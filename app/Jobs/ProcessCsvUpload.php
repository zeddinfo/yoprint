<?php

namespace App\Jobs;

use App\Events\UploadProcessed;
use App\Models\Upload;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function handle()
    {
        Log::info("Mulai proses upload CSV: {$this->upload->file_name}");

         $this->upload->update(['status' => 'processing']);
         event(new UploadProcessed($this->upload));

        $path = storage_path('app/uploads/' . $this->upload->file_name);

        Log::info("Membaca file CSV dari: {$path}");

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement());
        $records = $stmt->process($csv);

        Log::info("Jumlah baris yang akan diproses: " . count($records));

        foreach ($records as $index => $row) {
            // bersihkan non-UTF8
            $row = array_map(function($value) {
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }, $row);

            Product::updateOrCreate(
                ['unique_key' => $row['UNIQUE_KEY']],
                [
                    'product_title' => $row['PRODUCT_TITLE'] ?? null,
                    'product_description' => $row['PRODUCT_DESCRIPTION'] ?? null,
                    'style_number' => $row['STYLE#'] ?? null,
                    'mainframe_color' => $row['SANMAR_MAINFRAME_COLOR'] ?? null,
                    'size' => $row['SIZE'] ?? null,
                    'color_name' => $row['COLOR_NAME'] ?? null,
                    'piece_price' => $row['PIECE_PRICE'] ?? null,
                ]
            );

            Log::info("Diproses baris ke-" . ($index + 1) . " | UNIQUE_KEY: " . $row['UNIQUE_KEY']);
        }

        
        $this->upload->update(['status' => 'completed']);
        event(new UploadProcessed($this->upload));
        Log::info("Upload CSV selesai: {$this->upload->file_name}");
    }
}
