<?php
$settingsFile = 'f:/wamp/www/checkout/resources/views/affiliate/settings.blade.php';
$subFile = 'f:/wamp/www/checkout/resources/views/affiliate/sub_affiliates.blade.php';

// 1. Update settings.blade.php
if (file_exists($settingsFile)) {
    $content = file_get_contents($settingsFile);
    $search = '<div class="col-md-6">
                        <label class="form-label">Hero Title</label>';
    $replace = '<div class="col-md-6">
                        <label class="form-label">Checkout Page Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" id="settings_slug" data-ignore="{{ $affiliate->id }}" value="{{ old(\'slug\', $affiliate->slug) }}" required>
                        <div id="settings_slug_feedback" class="form-text mt-1"></div>
                    </div>
                    ' . $search;
    
    if (strpos($content, 'name="slug"') === false) {
        $content = str_replace($search, $replace, $content);
        
        $js = '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const slugInput = document.getElementById("settings_slug");
            const feedback = document.getElementById("settings_slug_feedback");
            const form = slugInput.closest("form");
            const submitBtn = form.querySelector("button[type=\'submit\']");
            let debounceTimer;

            slugInput.addEventListener("input", function() {
                clearTimeout(debounceTimer);
                const val = this.value.trim();
                const ignoreId = this.getAttribute("data-ignore");
                
                if (!val) {
                    feedback.innerHTML = "";
                    submitBtn.disabled = false;
                    return;
                }

                feedback.innerHTML = "<span class=\'text-muted\'>Checking availability...</span>";
                submitBtn.disabled = true;

                debounceTimer = setTimeout(() => {
                    fetch("{{ route(\'affiliate.portal.check-slug\') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ slug: val, ignore_id: ignoreId })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.available) {
                            feedback.innerHTML = "<span class=\'text-success\'><i class=\'bx bx-check\'></i> Slug is available!</span>";
                            submitBtn.disabled = false;
                        } else {
                            feedback.innerHTML = "<span class=\'text-danger\'><i class=\'bx bx-x\'></i> Slug is already taken.</span>";
                            submitBtn.disabled = true;
                        }
                    })
                    .catch(() => {
                        feedback.innerHTML = "";
                        submitBtn.disabled = false;
                    });
                }, 400);
            });
        });
        </script>
        ';
        $content = str_replace('</x-app-layout>', $js . "\n</x-app-layout>", $content);
        file_put_contents($settingsFile, $content);
        echo "Updated settings.blade.php\n";
    } else {
        echo "settings.blade.php already has slug input.\n";
    }
}

// 2. Update sub_affiliates.blade.php
if (file_exists($subFile)) {
    $content = file_get_contents($subFile);
    
    // Add to Create Modal
    $createSearch = '<div class="col-md-6">
                        <label class="form-label text-white">Email Address <span class="text-danger">*</span></label>';
    $createReplace = '<div class="col-md-6">
                        <label class="form-label text-white">Checkout Page Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control slug-input" placeholder="e.g., my-slug" required>
                        <div class="form-text mt-1 slug-feedback"></div>
                    </div>
                    ' . $createSearch;
    
    if (strpos($content, 'name="slug"') === false) {
        $content = str_replace($createSearch, $createReplace, $content);

        // Add to Edit Modal
        $editSearch = '<div class="col-md-6">
                        <label class="form-label text-white">Email Address</label>';
        $editReplace = '<div class="col-md-6">
                        <label class="form-label text-white">Checkout Page Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control slug-input border-warning shadow-sm" data-ignore="{{ $sub->id }}" value="{{ $sub->slug }}" required>
                        <div class="form-text mt-1 slug-feedback"></div>
                    </div>
                    ' . $editSearch;
        $content = str_replace($editSearch, $editReplace, $content);

        $js = '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".slug-input").forEach(input => {
                const feedback = input.nextElementSibling;
                const form = input.closest("form");
                const submitBtn = form.querySelector("button[type=\'submit\']");
                let debounceTimer;

                input.addEventListener("input", function() {
                    clearTimeout(debounceTimer);
                    const val = this.value.trim();
                    const ignoreId = this.getAttribute("data-ignore");
                    
                    if (!val) {
                        feedback.innerHTML = "";
                        submitBtn.disabled = false;
                        return;
                    }

                    feedback.innerHTML = "<span class=\'text-muted\'>Checking...</span>";
                    submitBtn.disabled = true;

                    debounceTimer = setTimeout(() => {
                        fetch("{{ route(\'affiliate.portal.check-slug\') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ slug: val, ignore_id: ignoreId })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.available) {
                                feedback.innerHTML = "<span class=\'text-success\'><i class=\'bx bx-check\'></i> Available</span>";
                                submitBtn.disabled = false;
                            } else {
                                feedback.innerHTML = "<span class=\'text-danger\'><i class=\'bx bx-x\'></i> Taken</span>";
                                submitBtn.disabled = true;
                            }
                        })
                        .catch(() => {
                            feedback.innerHTML = "";
                            submitBtn.disabled = false;
                        });
                    }, 400);
                });
            });
        });
        </script>
        ';
        $content = str_replace('</x-app-layout>', $js . "\n</x-app-layout>", $content);
        file_put_contents($subFile, $content);
        echo "Updated sub_affiliates.blade.php\n";
    } else {
        echo "sub_affiliates.blade.php already has slug input.\n";
    }
}
?>
