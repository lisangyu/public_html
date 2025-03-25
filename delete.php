<?php
// 文件路径
$file_path = '/home/s2746775/public_html/website/tmp/seq_67e2fb278c021.fasta';

// 检查文件是否存在
if (file_exists($file_path)) {
    // 尝试删除文件
    $result = unlink($file_path);
    
    // 如果删除成功
    if ($result) {
        echo "文件已成功删除: $file_path\n";
    } else {
        echo "删除文件时出错: $file_path\n";
        // 如果无法删除，尝试使用 Web 服务器的权限执行删除命令
        $command = 'sudo rm -f ' . escapeshellarg($file_path);
        
        // 执行删除命令
        exec($command, $output, $status);
        
        // 检查命令执行状态
        if ($status === 0) {
            echo "通过命令删除文件成功: $file_path\n";
        } else {
            echo "命令执行失败，状态码: $status\n";
            echo "输出: " . implode("\n", $output) . "\n";
        }
    }
} else {
    echo "文件不存在: $file_path\n";
}
?>
