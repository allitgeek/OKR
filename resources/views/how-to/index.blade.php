<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('How to Use the OKR System') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 space-y-8">

                    <section>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">What are OKRs?</h3>
                        <p class="text-lg text-gray-700">
                            OKR (Objectives and Key Results) is a goal-setting framework that helps organizations define ambitious goals and track their progress. It's designed to align teams and individuals around a common purpose, ensuring everyone is moving in the same direction.
                        </p>
                        <div class="mt-4 grid md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-semibold text-blue-800">Objectives (O)</h4>
                                <p>Objectives are ambitious, qualitative goals that state **what** you want to achieve. They should be inspiring and challenging.</p>
                                <p class="mt-2 text-sm text-gray-600"><strong>Example:</strong> "Launch a world-class customer support experience."</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-semibold text-green-800">Key Results (KR)</h4>
                                <p>Key Results are quantitative, measurable outcomes that show **how** you will achieve your Objective. They define success.</p>
                                <p class="mt-2 text-sm text-gray-600"><strong>Example:</strong> "Achieve a customer satisfaction (CSAT) score of 95%."</p>
                            </div>
                        </div>
                    </section>

                    <hr class="my-8">

                    <section>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">How to Use This Portal: A Complete Guide</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 1: Understand the OKR Cycle</h4>
                                <p class="mt-2 text-gray-600">
                                    The company operates on quarterly OKR cycles (e.g., Q1-2025, Q2-2025). You can view the current and upcoming cycles on the <a href="{{ route('okr-cycles.index') }}" class="text-indigo-600 hover:underline">OKR Cycles</a> page. <strong>All objectives are automatically linked to the current active cycle</strong> when created, ensuring proper tracking and reporting.
                                </p>
                                <div class="mt-3 bg-blue-50 p-3 rounded border-l-4 border-blue-400">
                                    <p class="text-sm text-blue-800"><strong>🎯 Pro Tip:</strong> Visit the <a href="{{ route('okr.dashboard') }}" class="text-blue-600 hover:underline">OKR Dashboard</a> for a comprehensive overview of your organization's current cycle performance.</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 2: Create a New Objective</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li>Navigate to the <a href="{{ route('objectives.index') }}" class="text-indigo-600 hover:underline">Objectives</a> page and click "Create Objective".</li>
                                    <li><strong>Write a strong Objective:</strong> Make it ambitious and inspiring (e.g., "Transform our customer experience").</li>
                                    <li><strong>Assign it to a cycle:</strong> Select the correct quarter to ensure proper tracking.</li>
                                    <li><strong>Set the level:</strong> Choose Individual, Team, or Company to establish the scope of impact.</li>
                                    <li><strong>Choose OKR type:</strong> Select "Committed" (must achieve) or "Aspirational" (stretch goals with 60-70% success rate).</li>
                                    <li><strong>Align with Parent Objective (Optional):</strong> Link to higher-level goals to create organizational alignment and cascading.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 3: Add Key Results with Advanced Features</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li>From the Objective's page, click "Create Key Result".</li>
                                    <li><strong>Make it measurable:</strong> Every Key Result must have specific numbers (e.g., from 50 to 100).</li>
                                    <li><strong>Choose KR type:</strong>
                                        <ul class="ml-6 mt-2 list-disc list-inside text-sm">
                                            <li><strong>Positive:</strong> Increase/Improve (more is better)</li>
                                            <li><strong>Negative:</strong> Decrease/Reduce (less is better)</li>
                                            <li><strong>Baseline:</strong> Maintain (keep at target level)</li>
                                            <li><strong>Milestone:</strong> Complete/Launch (binary achievement)</li>
                                        </ul>
                                    </li>
                                    <li><strong>Assign ownership:</strong> Assign to the person responsible for updating progress. The assignee can update progress, while only the objective owner can edit the definition.</li>
                                    <li><strong>Set weight:</strong> If some Key Results are more critical, assign higher weights (default is 1.0). This affects the overall objective score calculation.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 4: Track Progress with Check-ins</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li><strong>Regular Updates:</strong> Update the "Current Value" of your Key Results regularly. This automatically recalculates progress.</li>
                                    <li><strong>Formal Check-ins:</strong> Use the <a href="{{ route('okr-check-ins.create') }}" class="text-indigo-600 hover:underline">Check-in System</a> for structured progress reporting with:
                                        <ul class="ml-6 mt-2 list-disc list-inside text-sm">
                                            <li>Progress notes and updates</li>
                                            <li>Confidence level (High/Medium/Low)</li>
                                            <li>Challenges and blockers</li>
                                            <li>Next steps and action items</li>
                                            <li>Risk factors identification</li>
                                        </ul>
                                    </li>
                                    <li><strong>Quick Check-ins:</strong> Use the quick check-in feature for fast confidence and progress updates.</li>
                                    <li><strong>Check-in Types:</strong> Choose from Weekly, Bi-weekly, Monthly, Quarterly, or Ad-hoc based on your needs.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 5: Manage Tasks and Actions</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li>Break down Key Results into actionable <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:underline">Tasks</a> for better execution.</li>
                                    <li><strong>Task Assignment:</strong> Assign tasks to team members with due dates and priorities.</li>
                                    <li><strong>Task Acceptance:</strong> Assignees can accept or reject task assignments with comments.</li>
                                    <li><strong>Progress Sync:</strong> Task completion automatically updates related Key Result progress.</li>
                                    <li><strong>Escalation:</strong> Overdue tasks are automatically flagged for management attention.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 6: Monitor Performance and Analytics</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li><strong>OKR Dashboard:</strong> Get real-time insights into cycle progress, at-risk objectives, and team performance.</li>
                                    <li><strong>OKR Scoring:</strong> Understand the 0.0-1.0 scoring system where 0.7+ is considered successful.</li>
                                    <li><strong>Grade System:</strong> Track performance with grades (A: 0.9+, B: 0.7+, C: 0.5+, D: 0.3+, F: <0.3).</li>
                                    @can('view-analytics')
                                    <li><strong>Advanced Analytics:</strong> Access detailed <a href="{{ route('analytics.dashboard') }}" class="text-indigo-600 hover:underline">Analytics Dashboard</a> for team performance insights, trends, and predictive analytics.</li>
                                    @endcan
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 7: Collaborate and Communicate</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li><strong>Comments:</strong> Add comments to objectives and key results for team discussions.</li>
                                    <li><strong>Attachments:</strong> Upload relevant files and documents to provide context.</li>
                                    <li><strong>Team Visibility:</strong> All team members can view company objectives for transparency.</li>
                                    <li><strong>Notifications:</strong> Stay updated with automatic notifications for task assignments and updates.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <hr class="my-8">

                    <section>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">User Roles and Permissions</h3>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <h4 class="font-semibold text-purple-800">Super Admin</h4>
                                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                                    <li>• Full system access</li>
                                    <li>• User and role management</li>
                                    <li>• Analytics dashboard</li>
                                    <li>• Company settings</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-semibold text-blue-800">Manager</h4>
                                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                                    <li>• Create and manage objectives</li>
                                    <li>• Assign tasks to team</li>
                                    <li>• View team performance</li>
                                    <li>• Manage team settings</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-semibold text-green-800">Team Member</h4>
                                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                                    <li>• View company objectives</li>
                                    <li>• Update assigned key results</li>
                                    <li>• Accept/complete tasks</li>
                                    <li>• Create check-ins</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <hr class="my-8">

                    <section>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Best Practices</h3>
                        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                            <ul class="space-y-3 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-yellow-600 mr-2">📊</span>
                                    <span><strong>Set measurable targets:</strong> Every Key Result should have specific numbers and clear success criteria.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-yellow-600 mr-2">🎯</span>
                                    <span><strong>Aim for 70% achievement:</strong> OKRs should be ambitious. 100% completion means you set the bar too low.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-yellow-600 mr-2">📅</span>
                                    <span><strong>Regular check-ins:</strong> Update progress weekly and conduct formal check-ins bi-weekly or monthly.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-yellow-600 mr-2">🔗</span>
                                    <span><strong>Align objectives:</strong> Ensure team and individual objectives support company-level goals.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-yellow-600 mr-2">💬</span>
                                    <span><strong>Communicate transparently:</strong> Use comments and check-ins to keep everyone informed of progress and challenges.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-yellow-600 mr-2">⚡</span>
                                    <span><strong>Focus on outcomes:</strong> Key Results should measure impact and outcomes, not just activities or tasks.</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <hr class="my-8">

                    <section>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Quick Reference Links</h3>
                        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('okr.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
                                <div class="text-2xl mb-2">🎯</div>
                                <div class="font-semibold">OKR Dashboard</div>
                            </a>
                            <a href="{{ route('objectives.index') }}" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
                                <div class="text-2xl mb-2">📋</div>
                                <div class="font-semibold">My Objectives</div>
                            </a>
                            <a href="{{ route('okr-check-ins.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-colors">
                                <div class="text-2xl mb-2">✅</div>
                                <div class="font-semibold">Quick Check-in</div>
                            </a>
                            <a href="{{ route('tasks.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white p-4 rounded-lg text-center transition-colors">
                                <div class="text-2xl mb-2">📝</div>
                                <div class="font-semibold">My Tasks</div>
                            </a>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 