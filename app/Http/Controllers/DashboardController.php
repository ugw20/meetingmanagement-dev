<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\ActionItem;
use App\Models\Department;
use App\Models\User;
use App\Models\MeetingType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get period from request or default to 30 days
        $actionPeriod = $request->get('action_period', 30);
        $meetingPeriod = $request->get('meeting_period', 30);
        
        // Statistik Action Items dengan filter berdasarkan role
        $actionItemsQuery = ActionItem::query();
        
        if ($user->isAdmin()) {
            // Admin bisa lihat semua - tidak perlu filter tambahan
        } elseif ($user->isManager()) {
            // Manager hanya bisa lihat yang mereka buat dan yang ditugaskan ke mereka
            $actionItemsQuery->where(function($query) use ($user) {
                $query->whereHas('meeting', function($q) use ($user) {
                    $q->where('organizer_id', $user->id);
                })->orWhere('assigned_to', $user->id);
            });
        } else {
            // User biasa hanya bisa lihat yang ditugaskan ke mereka
            $actionItemsQuery->where('assigned_to', $user->id);
        }

        $totalActions = $actionItemsQuery->count();
        $completedActions = (clone $actionItemsQuery)->where('status', 'completed')->count();
        $inProgressActions = (clone $actionItemsQuery)->where('status', 'in_progress')->count();
        $pendingActions = (clone $actionItemsQuery)->where('status', 'pending')->count();
        $overdueActions = (clone $actionItemsQuery)->where('due_date', '<', now())
            ->whereIn('status', ['pending', 'in_progress'])->count();

        // Statistik Meeting dengan filter berdasarkan role
        $meetingsQuery = Meeting::query();
        
        if ($user->isAdmin()) {
            // Admin bisa lihat semua meeting
        } elseif ($user->isManager()) {
            // Manager bisa lihat meeting yang mereka buat atau yang mereka ikuti
            $meetingsQuery->where(function($query) use ($user) {
                $query->where('organizer_id', $user->id)
                      ->orWhereHas('participants', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            });
        } else {
            // User biasa hanya bisa lihat meeting yang mereka ikuti atau yang di departemen mereka
            $meetingsQuery->where(function($query) use ($user) {
                $query->whereHas('participants', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orWhere('organizer_id', $user->id)
                  ->orWhere('department_id', $user->department_id);
            });
        }

        $totalMeetings = $meetingsQuery->count();
        $scheduledMeetings = (clone $meetingsQuery)->where('status', 'scheduled')->count();
        $ongoingMeetings = (clone $meetingsQuery)->where('status', 'ongoing')->count();
        $completedMeetings = (clone $meetingsQuery)->where('status', 'completed')->count();

        // Meeting yang akan datang dengan filter role
        $upcomingMeetings = Meeting::with(['meetingType', 'organizer'])
            ->where('start_time', '>', now())
            ->where(function($query) use ($user) {
                if ($user->isAdmin()) {
                    // Admin lihat semua
                } elseif ($user->isManager()) {
                    $query->where('organizer_id', $user->id)
                          ->orWhereHas('participants', function($q) use ($user) {
                              $q->where('user_id', $user->id);
                          });
                } else {
                    $query->whereHas('participants', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->orWhere('organizer_id', $user->id)
                      ->orWhere('department_id', $user->department_id);
                }
            })
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Tugas yang perlu perhatian dengan filter role
        $attentionActions = ActionItem::with(['meeting', 'assignedTo'])
            ->where(function($query) use ($user) {
                if ($user->isAdmin()) {
                    // Admin lihat semua
                } elseif ($user->isManager()) {
                    $query->whereHas('meeting', function($q) use ($user) {
                        $q->where('organizer_id', $user->id);
                    })->orWhere('assigned_to', $user->id);
                } else {
                    $query->where('assigned_to', $user->id);
                }
            })
            ->where(function($query) {
                $query->where('due_date', '<', now())
                      ->orWhere('priority', 3); // High priority
            })
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Tindak Lanjut Terbaru dengan filter role
        $recentActionItems = ActionItem::with(['meeting', 'assignedTo', 'department'])
            ->where(function($query) use ($user) {
                if ($user->isAdmin()) {
                    // Admin lihat semua
                } elseif ($user->isManager()) {
                    $query->whereHas('meeting', function($q) use ($user) {
                        $q->where('organizer_id', $user->id);
                    })->orWhere('assigned_to', $user->id);
                } else {
                    $query->where('assigned_to', $user->id);
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Statistik Tugas per User (hanya untuk admin dan manager)
        $userAssignmentStats = [];
        if ($user->isAdmin() || $user->isManager()) {
            $userAssignmentStats = User::with(['department'])
                ->withCount([
                    'assignedActions as total_assigned',
                    'assignedActions as completed_actions' => function($query) {
                        $query->where('status', 'completed');
                    },
                    'assignedActions as pending_actions' => function($query) {
                        $query->where('status', 'pending');
                    },
                    'assignedActions as in_progress_actions' => function($query) {
                        $query->where('status', 'in_progress');
                    }
                ])
                ->whereHas('assignedActions')
                ->orderBy('total_assigned', 'desc')
                ->limit(6)
                ->get();
        }

        // Data untuk trend charts dengan filter role
        $actionTrendData = $this->getActionTrendData($actionPeriod, $user);
        $meetingTrendData = $this->getMeetingTrendData($meetingPeriod, $user);

        return view('dashboard.index', compact(
            'totalActions',
            'completedActions',
            'inProgressActions',
            'pendingActions',
            'overdueActions',
            'totalMeetings',
            'scheduledMeetings',
            'ongoingMeetings',
            'completedMeetings',
            'userAssignmentStats',
            'upcomingMeetings',
            'attentionActions',
            'recentActionItems',
            'actionTrendData',
            'meetingTrendData',
            'actionPeriod',
            'meetingPeriod'
        ));
    }

    /**
     * Get action items trend data dengan filter role
     */
    private function getActionTrendData($days = 30, $user = null)
    {
        $trendData = [
            'labels' => [],
            'created' => [],
            'completed' => [],
            'overdue' => []
        ];
        
        if ($days <= 7) {
            // Data per hari untuk 7 hari terakhir
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateString = $date->format('Y-m-d');
                $trendData['labels'][] = $date->format('d M');
                
                // Query dengan filter role
                $createdQuery = ActionItem::whereDate('created_at', $dateString);
                $completedQuery = ActionItem::whereDate('completed_at', $dateString);
                $overdueQuery = ActionItem::where('due_date', '<', $dateString)
                    ->whereIn('status', ['pending', 'in_progress']);
                
                // Apply role filters
                $this->applyRoleFilter($createdQuery, $user);
                $this->applyRoleFilter($completedQuery, $user);
                $this->applyRoleFilter($overdueQuery, $user);
                
                $trendData['created'][] = $createdQuery->count();
                $trendData['completed'][] = $completedQuery->count();
                $trendData['overdue'][] = $overdueQuery->count();
            }
        } else {
            // Data per minggu untuk periode lebih dari 7 hari
            $weeks = ceil($days / 7);
            
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();
                
                $weekLabel = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');
                $trendData['labels'][] = $weekLabel;
                
                // Query dengan filter role
                $createdQuery = ActionItem::whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $completedQuery = ActionItem::whereBetween('completed_at', [$startOfWeek, $endOfWeek]);
                $overdueQuery = ActionItem::where('due_date', '<', $endOfWeek)
                    ->whereIn('status', ['pending', 'in_progress']);
                
                // Apply role filters
                $this->applyRoleFilter($createdQuery, $user);
                $this->applyRoleFilter($completedQuery, $user);
                $this->applyRoleFilter($overdueQuery, $user);
                
                $trendData['created'][] = $createdQuery->count();
                $trendData['completed'][] = $completedQuery->count();
                $trendData['overdue'][] = $overdueQuery->count();
            }
        }
        
        return $trendData;
    }

    /**
     * Get meetings trend data dengan filter role
     */
    private function getMeetingTrendData($days = 30, $user = null)
    {
        $trendData = [
            'labels' => [],
            'created' => [],
            'completed' => [],
            'scheduled' => []
        ];
        
        if ($days <= 7) {
            // Data per hari untuk 7 hari terakhir
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateString = $date->format('Y-m-d');
                $trendData['labels'][] = $date->format('d M');
                
                // Query dengan filter role
                $createdQuery = Meeting::whereDate('created_at', $dateString);
                $completedQuery = Meeting::whereDate('end_time', $dateString)
                    ->where('status', 'completed');
                $scheduledQuery = Meeting::whereDate('start_time', $dateString)
                    ->whereIn('status', ['scheduled', 'ongoing']);
                
                // Apply role filters
                $this->applyRoleFilter($createdQuery, $user, 'meeting');
                $this->applyRoleFilter($completedQuery, $user, 'meeting');
                $this->applyRoleFilter($scheduledQuery, $user, 'meeting');
                
                $trendData['created'][] = $createdQuery->count();
                $trendData['completed'][] = $completedQuery->count();
                $trendData['scheduled'][] = $scheduledQuery->count();
            }
        } else {
            // Data per minggu untuk periode lebih dari 7 hari
            $weeks = ceil($days / 7);
            
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();
                
                $weekLabel = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');
                $trendData['labels'][] = $weekLabel;
                
                // Query dengan filter role
                $createdQuery = Meeting::whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $completedQuery = Meeting::whereBetween('end_time', [$startOfWeek, $endOfWeek])
                    ->where('status', 'completed');
                $scheduledQuery = Meeting::whereBetween('start_time', [$startOfWeek, $endOfWeek])
                    ->whereIn('status', ['scheduled', 'ongoing']);
                
                // Apply role filters
                $this->applyRoleFilter($createdQuery, $user, 'meeting');
                $this->applyRoleFilter($completedQuery, $user, 'meeting');
                $this->applyRoleFilter($scheduledQuery, $user, 'meeting');
                
                $trendData['created'][] = $createdQuery->count();
                $trendData['completed'][] = $completedQuery->count();
                $trendData['scheduled'][] = $scheduledQuery->count();
            }
        }
        
        return $trendData;
    }

    /**
     * Apply role-based filter to query
     */
    private function applyRoleFilter($query, $user, $type = 'action')
    {
        if (!$user) {
            return;
        }

        if ($user->isAdmin()) {
            // Admin bisa lihat semua - tidak perlu filter
            return;
        }

        if ($type === 'action') {
            if ($user->isManager()) {
                // Manager hanya bisa lihat yang mereka buat dan yang ditugaskan ke mereka
                $query->where(function($q) use ($user) {
                    $q->whereHas('meeting', function($meetingQuery) use ($user) {
                        $meetingQuery->where('organizer_id', $user->id);
                    })->orWhere('assigned_to', $user->id);
                });
            } else {
                // User biasa hanya bisa lihat yang ditugaskan ke mereka
                $query->where('assigned_to', $user->id);
            }
        } else {
            // Meeting queries
            if ($user->isManager()) {
                // Manager bisa lihat meeting yang mereka buat atau yang mereka ikuti
                $query->where(function($q) use ($user) {
                    $q->where('organizer_id', $user->id)
                      ->orWhereHas('participants', function($participantQuery) use ($user) {
                          $participantQuery->where('user_id', $user->id);
                      });
                });
            } else {
                // User biasa hanya bisa lihat meeting yang mereka ikuti atau yang di departemen mereka
                $query->where(function($q) use ($user) {
                    $q->whereHas('participants', function($participantQuery) use ($user) {
                        $participantQuery->where('user_id', $user->id);
                    })->orWhere('organizer_id', $user->id)
                      ->orWhere('department_id', $user->department_id);
                });
            }
        }
    }

    /**
     * Get enhanced action trend data dengan filter role
     */
    private function getEnhancedActionTrendData($days = 7, $user = null)
    {
        $trendData = [
            'labels' => [],
            'created' => [],
            'completed' => [],
            'overdue' => [],
            'in_progress' => [],
            'cancelled' => []
        ];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $trendData['labels'][] = $date->format('D, d M');
            
            // Query dengan filter role
            $createdQuery = ActionItem::whereDate('created_at', $dateString);
            $completedQuery = ActionItem::whereDate('completed_at', $dateString);
            $overdueQuery = ActionItem::where('due_date', '<', $dateString)
                ->whereIn('status', ['pending', 'in_progress']);
            $inProgressQuery = ActionItem::whereDate('updated_at', $dateString)
                ->where('status', 'in_progress');
            $cancelledQuery = ActionItem::whereDate('updated_at', $dateString)
                ->where('status', 'cancelled');
            
            // Apply role filters
            $this->applyRoleFilter($createdQuery, $user);
            $this->applyRoleFilter($completedQuery, $user);
            $this->applyRoleFilter($overdueQuery, $user);
            $this->applyRoleFilter($inProgressQuery, $user);
            $this->applyRoleFilter($cancelledQuery, $user);
            
            $trendData['created'][] = $createdQuery->count();
            $trendData['completed'][] = $completedQuery->count();
            $trendData['overdue'][] = $overdueQuery->count();
            $trendData['in_progress'][] = $inProgressQuery->count();
            $trendData['cancelled'][] = $cancelledQuery->count();
        }
        
        return $trendData;
    }

    /**
     * Get enhanced meeting trend data dengan filter role
     */
    private function getEnhancedMeetingTrendData($days = 7, $user = null)
    {
        $trendData = [
            'labels' => [],
            'created' => [],
            'completed' => [],
            'scheduled' => [],
            'ongoing' => [],
            'cancelled' => []
        ];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $trendData['labels'][] = $date->format('D, d M');
            
            // Query dengan filter role
            $createdQuery = Meeting::whereDate('created_at', $dateString);
            $completedQuery = Meeting::whereDate('end_time', $dateString)
                ->where('status', 'completed');
            $scheduledQuery = Meeting::whereDate('start_time', $dateString)
                ->where('status', 'scheduled');
            $ongoingQuery = Meeting::whereDate('start_time', '<=', $dateString)
                ->whereDate('end_time', '>=', $dateString)
                ->where('status', 'ongoing');
            $cancelledQuery = Meeting::whereDate('updated_at', $dateString)
                ->where('status', 'cancelled');
            
            // Apply role filters
            $this->applyRoleFilter($createdQuery, $user, 'meeting');
            $this->applyRoleFilter($completedQuery, $user, 'meeting');
            $this->applyRoleFilter($scheduledQuery, $user, 'meeting');
            $this->applyRoleFilter($ongoingQuery, $user, 'meeting');
            $this->applyRoleFilter($cancelledQuery, $user, 'meeting');
            
            $trendData['created'][] = $createdQuery->count();
            $trendData['completed'][] = $completedQuery->count();
            $trendData['scheduled'][] = $scheduledQuery->count();
            $trendData['ongoing'][] = $ongoingQuery->count();
            $trendData['cancelled'][] = $cancelledQuery->count();
        }
        
        return $trendData;
    }

    /**
     * API endpoint for chart data (for AJAX updates)
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'action');
        $period = $request->get('period', 30);
        $enhanced = $request->get('enhanced', false);
        $user = auth()->user();
        
        if ($type === 'action') {
            if ($enhanced && $period <= 7) {
                $data = $this->getEnhancedActionTrendData($period, $user);
            } else {
                $data = $this->getActionTrendData($period, $user);
            }
        } else {
            if ($enhanced && $period <= 7) {
                $data = $this->getEnhancedMeetingTrendData($period, $user);
            } else {
                $data = $this->getMeetingTrendData($period, $user);
            }
        }
        
        return response()->json($data);
    }

    /**
     * Get real-time statistics for the dashboard dengan filter role
     */
    public function getRealTimeStats()
    {
        $user = auth()->user();
        
        // Query dengan filter role
        $totalActionsQuery = ActionItem::query();
        $pendingActionsQuery = ActionItem::where('status', 'pending');
        $completedTodayQuery = ActionItem::whereDate('completed_at', today());
        $meetingsTodayQuery = Meeting::whereDate('start_time', today());
        $overdueActionsQuery = ActionItem::where('due_date', '<', now())
            ->whereIn('status', ['pending', 'in_progress']);
        
        // Apply role filters
        $this->applyRoleFilter($totalActionsQuery, $user);
        $this->applyRoleFilter($pendingActionsQuery, $user);
        $this->applyRoleFilter($completedTodayQuery, $user);
        $this->applyRoleFilter($overdueActionsQuery, $user);
        $this->applyRoleFilter($meetingsTodayQuery, $user, 'meeting');
        
        $stats = [
            'total_actions' => $totalActionsQuery->count(),
            'pending_actions' => $pendingActionsQuery->count(),
            'completed_today' => $completedTodayQuery->count(),
            'meetings_today' => $meetingsTodayQuery->count(),
            'overdue_actions' => $overdueActionsQuery->count(),
        ];
        
        return response()->json($stats);
    }
}