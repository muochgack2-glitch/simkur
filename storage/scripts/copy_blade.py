import shutil
src = r'C:\Users\DMCenter\Music\SPMB2\E-KALDIK\storage\scripts\blade_content.txt'
dst = r'C:\Users\DMCenter\Music\SPMB2\E-KALDIK\resources\views\livewire\student-assessment\index.blade.php'
shutil.copy2(src, dst)
