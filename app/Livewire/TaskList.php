namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Carbon\Carbon;

class TaskList extends Component
{
    public $selectedDate; // Ngày được chọn
    public $tasks = []; // Khởi tạo mảng rỗng

    public function mount($date = null)
    {
        // Nếu có ngày truyền vào thì dùng, không thì dùng hôm nay
        $this->selectedDate = $date ?? Carbon::today()->toDateString();
        $this->loadTasks();
    }

    // Method để filter theo ngày
    public function filterByDate($date)
    {
        $this->selectedDate = $date;
        $this->loadTasks(); // Reload tasks
    }

    // Method load tasks theo ngày
    public function loadTasks()
    {
        // Reset tasks về mảng rỗng trước khi query
        $this->tasks = [];
        
        $date = Carbon::parse($this->selectedDate);
        
        // Query tasks theo ngày đã chọn
        $this->tasks = Task::whereDate('due_at', $date->toDateString())
            ->orWhere(function($query) use ($date) {
                $query->whereDate('start_at', $date->toDateString());
            })
            ->orderBy('start_at', 'asc')
            ->get();
            
        // Debug: Kiểm tra số lượng tasks
        // dd($this->tasks->count(), $this->selectedDate);
    }

    public function toggleComplete($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->is_completed = !$task->is_completed;
            $task->save();
            
            // Reload lại tasks sau khi toggle
            $this->loadTasks();
        }
    }

    public function render()
    {
        return view('livewire.task-list', [
            'tasks' => $this->tasks
        ]);
    }
}