<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Template Service
 * 
 * Handles email template processing, variable replacement, and template management.
 */
class TemplateService
{
    /**
     * Get email template by slug
     */
    public function getTemplate(string $slug): ?EmailTemplate
    {
        try {
            return EmailTemplate::where('slug', $slug)->first();
        } catch (Exception $e) {
            Log::error('Failed to get email template', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Process template with variables
     */
    public function processTemplate(string $template, array $variables = []): string
    {
        try {
            // Add default variables
            $defaultVariables = [
                'app_name' => config('app.name', 'HelpDesk'),
                'app_url' => config('app.url'),
                'current_year' => date('Y'),
                'current_date' => date('Y-m-d'),
                'current_time' => date('H:i:s'),
            ];

            $allVariables = array_merge($defaultVariables, $variables);

            // Process template variables using a more robust approach
            return $this->replaceVariables($template, $allVariables);

        } catch (Exception $e) {
            Log::error('Failed to process template', [
                'error' => $e->getMessage(),
                'variables' => array_keys($variables)
            ]);
            return $template; // Return original template on error
        }
    }

    /**
     * Replace variables in template
     */
    protected function replaceVariables(string $template, array $variables): string
    {
        // Extract style and script tags to avoid matching CSS/JS as variables
        $styleMatches = [];
        $scriptMatches = [];
        $placeholderPrefix = '___TEMPLATE_PLACEHOLDER___';
        
        // Temporarily replace style tags
        $template = preg_replace_callback('/<style[^>]*>.*?<\/style>/is', function($matches) use (&$styleMatches, $placeholderPrefix) {
            $placeholder = $placeholderPrefix . 'STYLE_' . count($styleMatches);
            $styleMatches[$placeholder] = $matches[0];
            return $placeholder;
        }, $template);
        
        // Temporarily replace script tags
        $template = preg_replace_callback('/<script[^>]*>.*?<\/script>/is', function($matches) use (&$scriptMatches, $placeholderPrefix) {
            $placeholder = $placeholderPrefix . 'SCRIPT_' . count($scriptMatches);
            $scriptMatches[$placeholder] = $matches[0];
            return $placeholder;
        }, $template);
        
        // Handle different variable formats: {variable}, {{variable}}, [variable]
        // Only match simple variable names (alphanumeric, underscore, dot) - exclude CSS properties
        $patterns = [
            '/\{\{([a-zA-Z_][a-zA-Z0-9_.]*)\}\}/',  // {{variable}} - double braces
            '/\{([a-zA-Z_][a-zA-Z0-9_.]*)\}/',      // {variable} - single braces (alphanumeric only)
            '/\[([a-zA-Z_][a-zA-Z0-9_.]*)\]/',      // [variable] - square brackets
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $template, $matches)) {
                foreach ($matches[1] as $index => $variableName) {
                    $placeholder = $matches[0][$index];
                    $value = $this->getVariableValue($variableName, $variables);
                    
                    $template = str_replace($placeholder, $value, $template);
                }
            }
        }
        
        // Restore style tags
        foreach ($styleMatches as $placeholder => $original) {
            $template = str_replace($placeholder, $original, $template);
        }
        
        // Restore script tags
        foreach ($scriptMatches as $placeholder => $original) {
            $template = str_replace($placeholder, $original, $template);
        }

        return $template;
    }

    /**
     * Get variable value with fallback handling
     */
    protected function getVariableValue(string $variableName, array $variables): string
    {
        // Clean variable name (remove any formatting)
        $cleanName = trim($variableName);
        
        // Skip CSS/HTML-like content (contains colons, semicolons, or special chars)
        if (preg_match('/[;:\{\}<>\/\\\]/', $cleanName) || strpos($cleanName, ' ') !== false) {
            // This looks like CSS/HTML code, not a variable - return as-is
            return '{' . $cleanName . '}';
        }
        
        // Check if variable exists
        if (isset($variables[$cleanName])) {
            $value = $variables[$cleanName];
            
            // Handle different value types
            if (is_array($value)) {
                return json_encode($value);
            }
            
            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }
            
            if (is_null($value)) {
                return '';
            }
            
            return (string) $value;
        }

        // Try common variations
        $variations = [
            strtolower($cleanName),
            strtoupper($cleanName),
            ucfirst($cleanName),
            lcfirst($cleanName),
        ];

        foreach ($variations as $variation) {
            if (isset($variables[$variation])) {
                return (string) $variables[$variation];
            }
        }

        // Only log warning for valid variable names (not CSS/HTML code)
        // Valid variable names are alphanumeric with underscores/dots
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $cleanName)) {
            Log::warning('Template variable not found', [
                'variable_name' => $variableName,
                'available_variables' => array_keys($variables)
            ]);
        }
        
        // Return empty string for missing variables

        return '';
    }

    /**
     * Validate template syntax
     */
    public function validateTemplate(string $template): array
    {
        $issues = [];
        
        try {
            // Check for properly formatted variable tags
            $validVariablePattern = '/\{[^}]+\}/';
            $allBraces = preg_match_all('/\{|\}/', $template);
            
            // Count valid variable patterns
            $validVariables = preg_match_all($validVariablePattern, $template);
            
            // If we have braces but no valid variables, there might be an issue
            if ($allBraces > 0 && $validVariables === 0) {
                // Check for malformed tags
                $malformedTags = preg_match_all('/\{[^}]*$/', $template);
                if ($malformedTags > 0) {
                    $issues[] = 'Unclosed template tags detected';
                }
            }

            // Check for empty variables
            if (preg_match_all('/\{([^}]*)\}/', $template, $matches)) {
                foreach ($matches[1] as $variable) {
                    if (empty(trim($variable))) {
                        $issues[] = 'Empty variable placeholder found: {}';
                    }
                }
            }

            // Check for common HTML issues
            if (strpos($template, '<html') === false) {
                $issues[] = 'Template missing HTML structure';
            }

        } catch (Exception $e) {
            $issues[] = 'Template validation error: ' . $e->getMessage();
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues
        ];
    }

    /**
     * Get available template variables
     */
    public function getAvailableVariables(): array
    {
        return [
            'user' => [
                'name' => 'User\'s full name',
                'first_name' => 'User\'s first name',
                'last_name' => 'User\'s last name',
                'email' => 'User\'s email address',
                'phone' => 'User\'s phone number',
            ],
            'ticket' => [
                'id' => 'Ticket ID',
                'uid' => 'Ticket unique identifier',
                'subject' => 'Ticket subject',
                'description' => 'Ticket description',
                'status' => 'Ticket status',
                'priority' => 'Ticket priority',
                'type' => 'Ticket type',
                'department' => 'Ticket department',
                'url' => 'Direct link to ticket',
            ],
            'system' => [
                'app_name' => 'Application name',
                'app_url' => 'Application URL',
                'current_year' => 'Current year',
                'current_date' => 'Current date',
                'current_time' => 'Current time',
            ],
            'notification' => [
                'sender_name' => 'Sender name',
                'message' => 'Notification message',
                'comment' => 'Comment content (for comment notifications)',
                'password' => 'Generated password (for new user notifications)',
            ]
        ];
    }

    /**
     * Preview template with sample data
     */
    public function previewTemplate(string $slug, array $customVariables = []): array
    {
        try {
            $template = $this->getTemplate($slug);
            if (!$template) {
                return [
                    'success' => false,
                    'message' => 'Template not found'
                ];
            }

            $sampleData = $this->getSampleData();
            $allVariables = array_merge($sampleData, $customVariables);
            
            $processedTemplate = $this->processTemplate($template->html, $allVariables);
            $validation = $this->validateTemplate($processedTemplate);

            return [
                'success' => true,
                'template' => $processedTemplate,
                'variables_used' => $this->extractUsedVariables($template->html),
                'validation' => $validation,
                'sample_data' => $allVariables
            ];

        } catch (Exception $e) {
            Log::error('Failed to preview template', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to preview template: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get sample data for template preview
     */
    protected function getSampleData(): array
    {
        return [
            'name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1-555-0123',
            'ticket_id' => '12345',
            'uid' => 'HD-001234',
            'subject' => 'Sample Ticket Subject',
            'description' => 'This is a sample ticket description for preview purposes.',
            'status' => 'Open',
            'priority' => 'High',
            'type' => 'Technical Support',
            'department' => 'IT Support',
            'url' => config('app.url') . '/dashboard/tickets/HD-001234',
            'sender_name' => 'HelpDesk System',
            'message' => 'This is a sample notification message.',
            'comment' => 'This is a sample comment for preview purposes.',
            'password' => 'SamplePassword123!',
        ];
    }

    /**
     * Extract variables used in template
     */
    protected function extractUsedVariables(string $template): array
    {
        $variables = [];
        
        if (preg_match_all('/\{([^}]+)\}/', $template, $matches)) {
            $variables = array_unique($matches[1]);
        }

        return $variables;
    }

    /**
     * Get all templates with their metadata
     */
    public function getAllTemplates(): array
    {
        try {
            $templates = EmailTemplate::all();
            
            return $templates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'slug' => $template->slug,
                    'details' => $template->details,
                    'language' => $template->language,
                    'variables_used' => $this->extractUsedVariables($template->html),
                    'validation' => $this->validateTemplate($template->html),
                ];
            })->toArray();

        } catch (Exception $e) {
            Log::error('Failed to get all templates', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
