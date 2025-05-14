<?php
namespace App\Livewire;

use App\Models\Aspirasi;
use App\Models\Balasan;
use App\Models\Komentar;
use Filament\Widgets\Widget;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class AspirasiThreadWidget extends Widget
{
    use WithFileUploads;

    protected static string $view = 'livewire.aspirasi-thread-widget';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 1, // mobile
            'md' => 2,      // tablet
            'lg' => 3,
            'xl' => 4,      // desktop besar
            '2xl' => 4,     // layar ekstra besar
        ];
    }


    public $commentContent = [];
    public $commentImage = [];
    public $commentAnon = [];
    public $showCommentForm = [];
    public $replyContent = [];
    public $replyImage = [];
    public $replyAnon = [];
    public $showReplyForm = [];
    public $showReplies = [];
    public $confirmDelete = null; // id komentar atau balasan yang akan dihapus
    public $deleteType = null; // 'komentar' atau 'balasan'
    public $showComments = [];           // [aspirasiId => bool]
    public $commentPage = [];            // [aspirasiId => int]
    public $replyPage = [];              // [komentarId => int]
    public $perPage = 5;

    public $confirmingDelete = null;

    

    protected function getViewData(): array
    {
        return [
            'aspirasis' => Aspirasi::with([
                'user',
                'komentar' => function ($query) {
                    $query->where('is_approved', true)->with([
                        'user',
                        'balasan' => function ($query) {
                            $query->where('is_approved', true)->with('user');
                        },
                    ]);
                },
            ])->where('is_approved', true)->latest()->get(),
        ];
    }


    public function postComment($aspirasiId)
    {
        $this->validate([
            "commentContent.$aspirasiId" => 'required|string|max:500',
            "commentImage.$aspirasiId" => 'nullable|image|max:1024',
            "commentAnon.$aspirasiId" => 'nullable|boolean',
        ]);

        $komentar = new Komentar([
            'id_user' => Auth::id(),
            'id_aspirasi' => $aspirasiId,
            'content' => $this->commentContent[$aspirasiId],
            'is_anonymous' => $this->commentAnon[$aspirasiId] ?? false,
            'is_approved' => true,
        ]);

        if (!empty($this->commentImage[$aspirasiId])) {
            $komentar->image = $this->commentImage[$aspirasiId]->store('komentar-images', 'public');
        }

        $komentar->save();

        unset($this->commentContent[$aspirasiId], $this->commentImage[$aspirasiId], $this->commentAnon[$aspirasiId]);
    }

    public function postReply($komentarId)
    {
        $this->validate([
            "replyContent.$komentarId" => 'required|string|max:500',
            "replyImage.$komentarId" => 'nullable|image|max:1024',
            "replyAnon.$komentarId" => 'nullable|boolean',
        ]);

        $balasan = new Balasan([
            'id_user' => Auth::id(),
            'id_komentar' => $komentarId,
            'content' => $this->replyContent[$komentarId],
            'is_anonymous' => $this->replyAnon[$komentarId] ?? false,
            'is_approved' => true,
        ]);

        if (!empty($this->replyImage[$komentarId])) {
            $balasan->image = $this->replyImage[$komentarId]->store('balasan-images', 'public');
        }

        $balasan->save();

        unset($this->replyContent[$komentarId], $this->replyImage[$komentarId], $this->replyAnon[$komentarId]);
    }

    public function toggleReplies($komentarId)
    {
        $this->showReplies[$komentarId] = !($this->showReplies[$komentarId] ?? false);
    }

    public function confirmDelete($type, $id)
    {
        $this->confirmDelete = $id;
        $this->deleteType = $type;
    }

    public function cancelDelete()
    {
        $this->confirmDelete = null;
        $this->deleteType = null;
    }

    // public function deleteConfirmed()
    // {
    //     if ($this->deleteType === 'komentar') {
    //         Komentar::where('id', $this->confirmDelete)->where('id_user', auth()->id())->delete();
    //     } elseif ($this->deleteType === 'balasan') {
    //         Balasan::where('id', $this->confirmDelete)->where('id_user', auth()->id())->delete();
    //     }

    //     $this->cancelDelete();
    // }
    public function toggleComments($aspirasiId)
    {
        $this->showComments[$aspirasiId] = !($this->showComments[$aspirasiId] ?? false);
        $this->commentPage[$aspirasiId] = 1;
    }

    public function loadMoreComments($aspirasiId)
    {
        $this->commentPage[$aspirasiId] = ($this->commentPage[$aspirasiId] ?? 1) + 1;
    }

    public function loadMoreReplies($komentarId)
    {
        $this->replyPage[$komentarId] = ($this->replyPage[$komentarId] ?? 1) + 1;
    }

    public function deleteConfirmed()
    {
        if (!$this->confirmingDelete)
            return;

        $type = $this->confirmingDelete['type'];
        $id = $this->confirmingDelete['id'];

        if ($type === 'komentar') {
            $komentar = Komentar::find($id);
            if ($komentar && $komentar->id_user == auth()->id()) {
                $komentar->delete();
            }
        } elseif ($type === 'balasan') {
            $balasan = Balasan::find($id);
            if ($balasan && $balasan->id_user == auth()->id()) {
                $balasan->delete();
            }
        }

        $this->confirmingDelete = null;
    }

}
