<?php
namespace App\Core;
use Exception;

class CSV
{
    private $file;
    private $path;
    private $delimiter;
    private $idColumn; // Column that stores the auto-increment ID
    private $headers;   // Cache for the headers

    // Constructor initializes the file path and delimiter (default is comma)
    public function __construct($file, $fullpath = null)
    {
        $this->path = $fullpath ?? AUDIT_PATH ?? null;
        
        // Add .csv extension if not present
        if (pathinfo($file, PATHINFO_EXTENSION) === '') {
            $file .= '.csv';
        }
        
        $this->file = $this->path ? "{$this->path}/{$file}" : $file;
        $this->delimiter = ',';
        $this->idColumn = 'ID'; // Changed to uppercase to match the header

        // Check if file exists (for reading existing files)
        // Create the file only if it doesn't exist
        if (!file_exists($this->file)) {
            file_put_contents($this->file, "ID\n"); // Start with just ID as the column header
        }

        // Cache the headers
        $this->headers = $this->getHeaders();
    }

    // Get all rows from the CSV as an associative array
    public function getAll()
    {
        $rows = [];
        $handle = fopen($this->file, 'r');

        // Read the header row
        $this->headers = fgetcsv($handle, 1000, $this->delimiter);

        while (($data = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
            // Ensure the data matches the number of headers by padding with empty values if necessary
            $data = array_pad($data, count($this->headers), '');
            $rows[] = array_combine($this->headers, $data);
        }

        fclose($handle);
        return $rows;
    }

    // Get a record by ID
    public function getById($id)
    {
        $rows = $this->getAll();

        foreach ($rows as $row) {
            if ($row[$this->idColumn] == $id) {
                return $row;
            }
        }

        return null;
    }

    // Insert a new record into the CSV
    public function insert(array $data)
    {
        // Automatically generate the next ID
        $id = $this->getNextId();
        $data[$this->idColumn] = $id;

        // Get current headers from the data keys
        $existingHeaders = $this->getHeaders();

        // Update headers with any new keys in the data
        $newHeaders = array_keys($data);
        $updatedHeaders = array_unique(array_merge($existingHeaders, $newHeaders));

        // Update the CSV file with new headers if necessary
        if ($updatedHeaders !== $existingHeaders) {
            $this->updateHeaders($updatedHeaders);
        }

        // Ensure the data matches the updated headers
        $row = [];
        foreach ($updatedHeaders as $header) {
            $row[$header] = $data[$header] ?? ''; // Use empty string if a column is missing in the data
        }

        // Append the new row to the CSV file
        $handle = fopen($this->file, 'a');
        fputcsv($handle, $row, $this->delimiter);
        fclose($handle);
    }

    // Insert multiple rows into the CSV
    public function insertMultiple($dataRows)
    {
        // Ensure that we update the headers before inserting rows
        foreach ($dataRows as $data) {
            $this->insert($data); // Reuse the existing insert method
        }
    }

    // Update headers in the CSV file
    private function updateHeaders($headers)
    {
        // Read all rows and write back the new headers
        $rows = $this->getAll();
        $handle = fopen($this->file, 'w');
        fputcsv($handle, $headers, $this->delimiter);

        // Re-write the rows with the new headers
        foreach ($rows as $row) {
            $row = array_pad($row, count($headers), ''); // Ensure rows have the same number of columns as headers
            fputcsv($handle, $row, $this->delimiter);
        }

        fclose($handle);
    }

    // Update a record based on ID
    public function update($id, $newData)
    {
        $rows = $this->getAll();
        $updated = false;

        foreach ($rows as &$row) {
            if ($row[$this->idColumn] == $id) {
                $row = array_merge($row, $newData);
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $this->writeData($rows);
        }
    }

    // Delete a record based on ID
    public function delete($id)
    {
        $rows = $this->getAll();
        $filteredRows = array_filter($rows, function ($row) use ($id) {
            return $row[$this->idColumn] != $id;
        });

        $this->writeData($filteredRows);
    }

    // Get the headers from the CSV file (cached after the first read)
    private function getHeaders()
    {
        if (empty($this->headers)) {
            $handle = fopen($this->file, 'r');
            $this->headers = fgetcsv($handle, 1000, $this->delimiter);
            fclose($handle);
        }
        return $this->headers;
    }

    // Write data back to the CSV file
    private function writeData($rows)
    {
        $handle = fopen($this->file, 'w');
        fputcsv($handle, $this->headers, $this->delimiter);

        foreach ($rows as $row) {
            fputcsv($handle, $row, $this->delimiter);
        }

        fclose($handle);
    }

    // Get the next available ID based on the highest ID in the file
    private function getNextId()
    {
        $rows = $this->getAll();
        $maxId = 0;

        foreach ($rows as $row) {
            if ((int) $row[$this->idColumn] > $maxId) {
                $maxId = (int) $row[$this->idColumn];
            }
        }

        return $maxId + 1;
    }


    public function getFieldNames()
    {
        return $this->headers; // Return the cached headers
    }


    // Get a random row from the CSV file
    public function getRandomRow()
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // If there are no rows, return null
        if (empty($rows)) {
            return null;
        }

        // Get a random index from the array of rows
        $randomIndex = array_rand($rows);

        // Return the random row
        return $rows[$randomIndex];
    }

    public function getRandomRows($count)
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // If there are no rows or the requested count is greater than available rows, return an empty array
        if (empty($rows) || $count <= 0) {
            return [];
        }

        // Ensure that the count doesn't exceed the number of rows available
        $count = min($count, count($rows));

        // Get the random keys of the rows
        $randomKeys = array_rand($rows, $count);

        // If $count is 1, array_rand returns a single value (not an array), so make sure it's an array
        if ($count == 1) {
            $randomKeys = [$randomKeys];
        }

        // Create an array to hold the random rows
        $randomRows = [];
        foreach ($randomKeys as $key) {
            $randomRows[] = $rows[$key];
        }

        // Return the random rows
        return $randomRows;
    }

    public function getRandomId()
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // If there are no rows, return null
        if (empty($rows)) {
            return null;
        }

        // Get a random index from the rows array
        $randomIndex = array_rand($rows);

        // Return the ID from the random row
        return $rows[$randomIndex][$this->idColumn];
    }

    public function clearData()
    {
        // Get the headers (field names) from the file
        $headers = $this->getHeaders();

        // Open the file in write mode and overwrite it with only the headers
        if (($handle = fopen($this->file, 'w')) !== FALSE) {
            // Write the headers to the file
            fputcsv($handle, $headers, $this->delimiter);
            fclose($handle);
        }
    }

    public function tableExists()
    {
        return file_exists($this->file);
    }

   

    public function addField($fieldName, $defaultValue = '')
    {
        // Get all rows from the CSV
        $rows = $this->getAll();

        // Add the new field to the headers if it doesn't already exist
        if (!in_array($fieldName, $this->headers)) {
            $this->headers[] = $fieldName;
        }

        // Update each row to include the new field with the default value
        foreach ($rows as &$row) {
            $row[$fieldName] = $defaultValue;
        }

        // Write the updated data back to the CSV file
        $this->writeData($rows);
    }
    

    // Add multiple fields (columns) to the CSV
    public function addFields(array $fields)
    {
        // Get all rows from the CSV
        $rows = $this->getAll();

        // Loop through each field to add it to the headers and rows
        foreach ($fields as $fieldName => $defaultValue) {
            // Add the field to the header if it doesn't already exist
            if (!in_array($fieldName, $this->headers)) {
                $this->headers[] = $fieldName;
            }

            // Add the new field to each row with the default value
            foreach ($rows as &$row) {
                $row[$fieldName] = $defaultValue;
            }
        }

        // Write the updated data back to the CSV file
        $this->writeData($rows);
    }


    public function countEmptyField($fieldName)
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Initialize a counter for empty fields
        $emptyCount = 0;

        // Check each row for an empty field value
        foreach ($rows as $row) {
            // If the field is empty (null, empty string, or doesn't exist), increment the counter
            if (empty($row[$fieldName])) {
                $emptyCount++;
            }
        }

        return $emptyCount;
    }

    public function getRowsWithFieldValue($fieldName)
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Filter rows where the specified field is not empty
        $filteredRows = array_filter($rows, function($row) use ($fieldName) {
            return !empty($row[$fieldName]);
        });

        // Re-index the filtered rows to ensure the keys are sequential
        return array_values($filteredRows);
    }

    public function getAllIds()
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Extract the ID values from the rows
        $ids = array_column($rows, $this->idColumn);

        return $ids;
    }

    public function getFirstRow()
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Return the first row if there are any rows, otherwise return null
        return !empty($rows) ? $rows[0] : null;
    }

    public function getLastRow()
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Return the last row if there are any rows, otherwise return null
        return !empty($rows) ? end($rows) : null;
    }

    public function deleteField($fieldName)
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Get the headers (field names) from the first row
        $headers = $this->getHeaders();

        // Check if the field exists in the headers
        if (in_array($fieldName, $headers)) {
            // Find the index of the field to be deleted
            $fieldIndex = array_search($fieldName, $headers);

            // Remove the field from the headers
            unset($headers[$fieldIndex]);

            // Remove the field from each row
            foreach ($rows as &$row) {
                unset($row[$fieldName]);  // Unset the column in each row
            }

            // Write the updated rows back to the CSV file
            $this->writeData($rows);
        } else {
            echo "Field '{$fieldName}' does not exist.\n";
        }
    }

    public function getRowCount()
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Return the number of rows excluding the header row
        return count($rows);
    }

    public function idExists($id)
    {
        // Get all rows from the CSV file
        $rows = $this->getAll();

        // Check if the ID exists in any row
        foreach ($rows as $row) {
            if ($row[$this->idColumn] == $id) {
                return true;  // ID exists
            }
        }

        return false;  // ID does not exist
    }

     // New Method: Get selected fields
     public function getSelectedFields(array $fields)
     {
         $rows = $this->getAll();
         $selectedData = [];
 
         foreach ($rows as $row) {
             $filteredRow = [];
             foreach ($fields as $field) {
                 if (isset($row[$field])) {
                     $filteredRow[$field] = $row[$field];
                 }
             }
             $selectedData[] = $filteredRow;
         }
 
         return $selectedData;
     }

     // New Method: Delete an array of IDs
    public function deleteByIds(array $ids)
    {
        $rows = $this->getAll();
        $filteredRows = array_filter($rows, function ($row) use ($ids) {
            return !in_array($row[$this->idColumn], $ids);
        });

        $this->writeData($filteredRows);
    }

    // Sort records by a specific field in ascending order
    public function sortByFieldAsc($field)
    {
        $rows = $this->getAll();
        
        // Ensure that the field exists in the headers
        if (!in_array($field, $this->headers)) {
            throw new Exception("Field '$field' does not exist in the CSV headers.");
        }

        // Sort the rows by the given field in ascending order
        usort($rows, function ($a, $b) use ($field) {
            return strcmp($a[$field], $b[$field]);
        });

        // Optionally, write the sorted data back to the file
        $this->writeData($rows);

        return $rows;
    }

    // Sort records by a specific field in descending order
    public function sortByFieldDesc($field)
    {
        $rows = $this->getAll();
        
        // Ensure that the field exists in the headers
        if (!in_array($field, $this->headers)) {
            throw new Exception("Field '$field' does not exist in the CSV headers.");
        }

        // Sort the rows by the given field in descending order
        usort($rows, function ($a, $b) use ($field) {
            return strcmp($b[$field], $a[$field]);
        });

        // Optionally, write the sorted data back to the file
        $this->writeData($rows);

        return $rows;
    }

    public function dumpAll()
    {
        // Get all rows from the CSV
        $rows = $this->getAll();

        // If there are no rows, show a message
        if (empty($rows)) {
            echo "No data available in the CSV file.";
            return;
        }

        // Loop through and print out each row
        foreach ($rows as $row) {
            echo "<pre>";
            print_r($row);  // You can replace this with var_dump($row) for more detailed output
            echo "</pre>";
        }
    }


    
}