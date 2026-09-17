import re

path = r'C:\Users\DMCenter\Music\SPMB2\E-KALDIK\app\Livewire\PklField\VisitMonitoring.php'

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Find and replace the old sort block
old_block = (
    "        \ = match(\->sortBy) {\n"
    "            'teacher_name'  => 'users.name',\n"
    "            'company_name'  => 'pkl_companies.name',\n"
    "            default         => 'pkl_visits.' . \->sortBy,\n"
    "        };\n"
    "        \ = \->orderBy(\, \->sortDir)->get();"
)

new_block = (
    "        // Kolom SQL: orderBy langsung; relasi: sort in-memory\n"
    "        \ = ['scheduled_date', 'actual_date', 'status'];\n"
    "        if (in_array(\->sortBy, \)) {\n"
    "            \ = \->orderBy(\->sortBy, \->sortDir)->get();\n"
    "        } else {\n"
    "            \ = \->orderBy('scheduled_date')->get();\n"
    "            \ = match(\->sortBy) {\n"
    "                'teacher_name' => \->sortDir === 'asc'\n"
    "                    ? \->sortBy(fn(\) => \->teacher?->name ?? '')\n"
    "                    : \->sortByDesc(fn(\) => \->teacher?->name ?? ''),\n"
    "                'company_name' => \->sortDir === 'asc'\n"
    "                    ? \->sortBy(fn(\) => \->company?->name ?? '')\n"
    "                    : \->sortByDesc(fn(\) => \->company?->name ?? ''),\n"
    "                default => \,\n"
    "            };\n"
    "        }"
)

if old_block in content:
    content = content.replace(old_block, new_block, 1)
    with open(path, 'w', encoding='utf-8', newline='') as f:
        f.write(content)
    print('OK - replaced')
else:
    print('NOT FOUND - check exact string')
