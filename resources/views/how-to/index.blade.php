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
                            OKR (Objectives and Key Results) is a goal-setting framework that helps organizations define ambitious goals and track their progress. It’s designed to align teams and individuals around a common purpose, ensuring everyone is moving in the same direction.
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
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">How to Use This Portal: A Step-by-Step Guide</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 1: Understand the OKR Cycle</h4>
                                <p class="mt-2 text-gray-600">
                                    The company operates on quarterly OKR cycles (e.g., Q1, Q2). You can view the current and upcoming cycles on the <a href="{{ route('okr-cycles.index') }}" class="text-indigo-600 hover:underline">OKR Cycles</a> page. All your Objectives must be linked to a cycle.
                                </p>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 2: Create a New Objective</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li>Navigate to the <a href="{{ route('objectives.index') }}" class="text-indigo-600 hover:underline">Objectives</a> page and click "Create Objective".</li>
                                    <li><strong>Write a strong Objective:</strong> Make it ambitious and inspiring.</li>
                                    <li><strong>Assign it to a cycle:</strong> Select the correct quarter.</li>
                                    <li><strong>Set the level:</strong> Is this an Individual, Team, or Company goal?</li>
                                    <li><strong>Align with a Parent Objective (Optional):</strong> If your Objective contributes to a higher-level goal, select it as the parent. This creates alignment.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 3: Add Key Results</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li>From the Objective's page, click "Create Key Result".</li>
                                    <li><strong>Make it measurable:</strong> A Key Result must have a number.</li>
                                    <li><strong>Define the metric:</strong> Set a start value and a target value (e.g., increase from 50 to 100).</li>
                                    <li><strong>Assign an owner:</strong> You can assign a Key Result to yourself or another person in the company. The owner is responsible for updating its progress.</li>
                                    <li><strong>Set a weight:</strong> If some Key Results are more important than others for the Objective, you can give them a higher weight. The default is 1.0.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 4: Update Progress with Check-ins</h4>
                                <ul class="mt-2 list-disc list-inside text-gray-600 space-y-1">
                                    <li>Regularly update the "Current Value" of your Key Results. This is called a "Check-in".</li>
                                    <li>When you update the value, the system automatically recalculates the progress of the Key Result and its parent Objective.</li>
                                    <li><strong>Confidence Level:</strong> During a check-in, set your confidence level. Are you on track (High), at risk (Medium), or off track (Low)? This helps identify problems early.</li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700">Step 5: Review the Dashboard</h4>
                                <p class="mt-2 text-gray-600">
                                    The <a href="{{ route('okr.dashboard') }}" class="text-indigo-600 hover:underline">OKR Dashboard</a> provides a high-level overview of the company's performance for the current cycle. Use it to see overall progress, identify at-risk OKRs, and celebrate successes.
                                </p>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 