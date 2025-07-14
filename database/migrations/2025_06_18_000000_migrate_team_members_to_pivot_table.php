<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all teams that still have the members column
        if (Schema::hasColumn('teams', 'members')) {
            $teams = DB::table('teams')->whereNotNull('members')->get();

            foreach ($teams as $team) {
                // Decode the JSON members column
                $memberIdsArray = json_decode($team->members, true);
                if (is_array($memberIdsArray)) {
                    foreach ($memberIdsArray as $memberId) {
                        // Ensure memberId is a valid integer/ID format
                        if (is_numeric($memberId)) {
                            DB::table('team_employees')->insertOrIgnore([
                                'team_id' => $team->id,
                                'employee_id' => (int)$memberId,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }

            // Remove the members column from teams table only if it exists
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('members');
            });
        } // End if hasColumn('teams', 'members')
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the members column only if it doesn't exist
        if (!Schema::hasColumn('teams', 'members')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->json('members')->nullable();
            });
        }

        // Get all teams
        // This part of the down migration is complex and depends on the team_employees table correctly populating.
        // It also assumes the Team model's members() relation is correctly defined for the pivot table.
        // For safety, if the migration failed partially, this might not work as expected.
        // Consider if this data rollback is critical or if a manual check is better after a failed up().
        /*
        $teams = Team::with('employeesViaPivot')->get(); // Assuming a temporary relation name

        foreach ($teams as $team) {
            if ($team->employeesViaPivot) {
                $memberIds = $team->employeesViaPivot->pluck('id')->toArray();
                DB::table('teams')->where('id', $team->id)->update(['members' => json_encode($memberIds)]);
            }
        }
        */
    }
}; 