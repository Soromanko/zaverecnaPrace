<form method="POST" class="mt-3">
    <div class="mb-3">
        <label for="subject" class="form-label">Předmět:</label>
        <input type="text" name="subject" id="subject" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="post" class="form-label">Nový příspěvek:</label>
        <textarea name="post" id="post" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Odeslat</button>
</form>