<div class="well card-footer"> 
    <div class="btn-group">
        <button class="shadow-none btn nav-link" onclick="location.href='about'"><i class="fas fa-info-circle mr-2"></i>About Us</button> 
        <button class="shadow-none btn nav-link" onclick="location.href='contact'"><i class="fas fa-address-card mr-2"></i>Contact Us</button>
    </div>
</div>
</div>



<script>
    $(document).ready(function () {
        $("#flash-msg").delay(7000).fadeOut("slow");
        //   $('#example').DataTable();
    });
    function updateTable() {
        $("#example").DataTable().destroy();
        $("#example").DataTable();
    }
    function redirect(href, attrMap) {
        let form = document.createElement("form");
         
        form.setAttribute("method", "POST");
        form.setAttribute("action", href);
        form.setAttribute("style", "display: none");
     
        const hiddenInput = document.createElement("input");
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("name", 'href');
        hiddenInput.setAttribute("value", href);
        form.appendChild(hiddenInput);
         
        for (const key of Object.keys(attrMap)) {
            const hiddenInput = document.createElement("input");
            hiddenInput.setAttribute("type", "hidden");
            hiddenInput.setAttribute("name", key);
            hiddenInput.setAttribute("value", attrMap[key]);
            form.appendChild(hiddenInput);
        }
         
        document.body.append(form);
        form.submit();
         
        return false;
    }
  </script>
</body>
</html>